<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        // Récupérer tous les messages, les plus récents en premier
        $messages = ContactMessage::orderBy('created_at', 'desc')->get();

        return view('admin.contact_messages.index', compact('messages'));
    }
    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return back()->with('success', __('Message deleted successfully.'));
    }
}
