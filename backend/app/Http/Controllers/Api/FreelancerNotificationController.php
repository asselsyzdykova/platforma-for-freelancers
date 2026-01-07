<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreelancerNotificationController extends Controller
{
    public function index()
    {
        return Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $notification->update([
            'is_read' => true
        ]);

        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    public function unreadCount()
    {
        return [
            'count' => Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count()
        ];
    }
}
