<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify all group members except the actor.
     *
     * @param Group $group
     * @param User $actor The user who triggered the action
     * @param string $title
     * @param string $message
     * @param string $type ('info', 'success', 'warning', 'error', etc.)
     * @return void
     */
    public function notifyGroup(Group $group, User $actor, string $title, string $message, string $type = 'info'): void
    {
        try {
            DB::transaction(function () use ($group, $actor, $title, $message, $type) {
                // Get all group users except the actor
                $userIds = $group->users()
                    ->where('users.id', '!=', $actor->id)
                    ->pluck('users.id')
                    ->toArray();

                if (empty($userIds)) {
                    return; // No one to notify
                }

                // Create the notification
                $notification = Notification::create([
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'group_id' => $group->id,
                ]);

                // Attach to users with unread status
                $notification->users()->attach($userIds);
            });
        } catch (\Exception $e) {
            Log::error('Failed to send group notification: ' . $e->getMessage());
        }
    }
}
