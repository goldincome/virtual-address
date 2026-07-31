<?php

namespace App\Http\Controllers\Auth;

use App\Mail\PasswordChangedEmail;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        try {
            Mail::to($request->user()->email)->send(new PasswordChangedEmail($request->user()));
        } catch (\Exception $e) {
            Log::error('PasswordChangedEmail send failed for user ' . $request->user()->id . ': ' . $e->getMessage());
        }

        return back()->with('status', 'password-updated');
    }
}
