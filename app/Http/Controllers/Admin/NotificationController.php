<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Afficher toutes les notifications
     */
    public function index()
    {
        $admin = auth()->user();

        $notifications = $admin->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $unreadCount = $admin->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return redirect()->back();
    }
    public function open($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        // Marquer comme lue si non lue
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // Rediriger vers l’URL de la notification
        return redirect(
            $notification->data['url'] ?? route('notifications.index')
        );
    }

    public function read($id)
    {
        return $this->markAsRead($id);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', __('All notifications have been marked as read.'));
    }

    /**
     * Supprimer une notification
     */
    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', __('Notification deleted'));
    }
}
