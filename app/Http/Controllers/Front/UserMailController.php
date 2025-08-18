<?php

namespace App\Http\Controllers\Front;

use App\Enums\MailStatusEnum;
use App\Enums\MailTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserMailRequest;
use App\Models\Mail;
use Illuminate\Support\Facades\Auth;

class UserMailController extends Controller
{
    public function index()
    {
        //$mails = Mail::where('user_id', Auth::id())
        $mails = Auth::user()->mails()->latest()
                     ->paginate(10);
        $mailStatuses = MailStatusEnum::class;
        //dd($mailStatuses::Pending->value);
        return view('front.mails.index', compact('mails', 'mailStatuses'));
    }

    public function create()
    {
        //$this->authorize('create', Mail::class);
        $mailTypes = MailTypeEnum::cases();
        return view('front.mails.create', compact('mailTypes'));
    }

    public function store(UserMailRequest $request)
    {
        //$this->authorize('create', Mail::class);

        Auth::user()->mails()->create($request->validated());

        return redirect()->route('mails.index')->with('success', 'Your mail request has been submitted successfully.');
    }

    public function show(Mail $mail)
    {
        //$this->authorize('view', $mail);
        return view('front.mails.show', compact('mail'));
    }

    public function edit(Mail $mail)
    {
        //$this->authorize('update', $mail);
        $mailTypes = MailTypeEnum::cases();
        return view('front.mails.edit', compact('mail', 'mailTypes'));
    }

    public function update(UserMailRequest $request, Mail $mail)
    {
        //$this->authorize('update', $mail);
        $mail->update($request->validated());
        return redirect()->route('mails.index')->with('success', 'Your mail request has been updated.');
    }

    public function destroy(Mail $mail)
    {
        //$this->authorize('delete', $mail);
        $mail->delete();
        return redirect()->route('mails.index')->with('success', 'Your mail request has been cancelled.');
    }


}

