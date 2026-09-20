<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Notifications\AdminNotification;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $message = ContactMessage::create($request->only('name', 'email', 'message'));

        // Notification + email à l'admin
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();
        if ($admin) {
            $admin->notify(new AdminNotification(
                'New Contact Message',
                $request->name . ' vous a envoyé un message.',
                route('admin.contact-messages.index')
            ));
        }

        // Envoi email directement
        Mail::raw($request->message, function ($mail) use ($request) {
            $mail->to('fatizaelouali@gmail.com')
                 ->subject('New Contact Message from ' . $request->name)
                 ->replyTo($request->email);
        });

        return back()->with('success', 'Your message has been sent successfully!');
    }
}
