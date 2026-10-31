<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            // No line breaks in name or email: both go into email headers.
            'name' => 'required|string|max:255|not_regex:/[\r\n]/',
            'email' => 'required|email|not_regex:/[\r\n]/',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $adminEmails = config('mail.admin_addresses');

        Mail::raw(
            "Contact form submission\n\n" .
            "From: {$validated['name']} <{$validated['email']}>\n" .
            "Phone: " . ($validated['phone'] ?? 'N/A') . "\n" .
            "Subject: " . ($validated['subject'] ?? 'N/A') . "\n\n" .
            "Message:\n" . $validated['message'],
            function ($message) use ($adminEmails, $validated) {
                $message->to($adminEmails)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('Website contact form - G&M Autospares');
            }
        );

        // Back to whichever form sent it: the Contact page, or Quick Contact on About.
        return back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}
