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
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:2000',
        ]);

        $adminEmail = config('mail.from.address', env('ADMIN_EMAIL', 'admin@example.com'));

        Mail::raw(
            "Contact form submission\n\n" .
            "From: {$validated['name']} <{$validated['email']}>\n" .
            "Phone: " . ($validated['phone'] ?? 'N/A') . "\n\n" .
            "Message:\n" . $validated['message'],
            function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('Website contact form - G&M Autospares');
            }
        );

        return redirect()->route('contact')->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}
