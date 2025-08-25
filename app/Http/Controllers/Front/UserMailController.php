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

    public function show(Mail $mail)
    {
        //$this->authorize('view', $mail);
        return view('front.mails.show', compact('mail'));
    }

    public function download(Mail $mail)
    {
        //$this->authorize('view', $mail);
        if($mail->mail_status->value !== MailStatusEnum::Scanned->value || !$mail->scan_upload_url) {
            return redirect()->route('mails.index')->with('error', 'The scanned mail is not available for download.');
        }

        return response()->download(public_path('storage/' . $mail->scan_upload_url));
    }


}

