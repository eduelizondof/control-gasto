<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $user = $request->user();

        // Ensure the notification belongs to the user
        if ($notification->users()->where('users.id', $user->id)->exists()) {
            $notification->users()->updateExistingPivot($user->id, [
                'read_at' => now(),
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Not found'], 404);
    }

    /**
     * Mark all notifications as read for the user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $unreadNotifications = $user->notifications()->wherePivotNull('read_at')->get();

        foreach ($unreadNotifications as $notification) {
            $notification->users()->updateExistingPivot($user->id, [
                'read_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
