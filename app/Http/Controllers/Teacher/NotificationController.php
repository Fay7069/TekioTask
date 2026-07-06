<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TekioNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ── Polled every 10s by teacher dashboard JS ──────────────
    public function unread()
    {
        $alerts = TekioNotification::where('user_id', Auth::id())
                                   ->where('type', 'alert')
                                   ->where('is_read', false)
                                   ->orderBy('sent_at', 'desc')
                                   ->get(['notification_id', 'message', 'sent_at']);

        return response()->json($alerts);
    }

    // ── Mark notification as read ─────────────────────────────
    public function markRead(TekioNotification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);
        $notification->update(['is_read' => true]);

        return response()->json(['status' => 'ok']);
    }
}
