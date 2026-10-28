<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
        ]);

        $user->update($request->only([
            'name',
            'email',
            'phone',
            'first_name',
            'last_name',
        ]));

        if ($user->wasChanged('email')) {
            $user->email_verified_at = null;
            $user->save();
        }

        return Redirect::route('admin.profile.edit')->with('success', 'Admin profile updated successfully.');
    }
}
