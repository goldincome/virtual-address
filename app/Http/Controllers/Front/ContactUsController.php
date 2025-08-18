<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    public function index()
    {
        // Display the contact us form
        return view('front.contact-us.index');
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'support_type' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);
        $data = [];
        // Process the contact us form submission (e.g., send an email)
        //unset($data['captcha']);
        $data['contactUs'] = $validated; //Message::create($data);

        try{
            Mail::send('emails.ContactUsAdminNotification', $data, function ($message){
                $message->to(config('app.admin_email'), 'NURUD - UK');
                $message->subject('New Message Virtual Address Website-NURUD');
                $message->replyTo('info@ninuk.co.uk');
            });
        } catch(\Throwable $e){
            return redirect()->back()->withErrors(['error' => 'Failed to send message. Please try again later.'])->withInput();
        }
        // Redirect back with a success message
        return redirect()->route('contact-us.index')->with('success', 'Your message has been sent successfully!');
    }
}
