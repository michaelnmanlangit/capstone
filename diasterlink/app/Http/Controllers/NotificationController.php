<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Check for new notifications
     */
    public function check(): JsonResponse
    {
        try {
            $unreadCount = Notification::where('user_id', Auth::id())
                ->where('read_at', null)
                ->count();

            $hasNew = $unreadCount > 0;

            return response()->json([
                'success' => true,
                'hasNew' => $hasNew,
                'count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Notification Check Failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'hasNew' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Get user's notifications
     */
    public function index(): JsonResponse
    {
        try {
            $notifications = Notification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'is_read' => !is_null($notification->read_at),
                        'created_at' => $notification->created_at->toISOString(),
                        'data' => $notification->data ? json_decode($notification->data, true) : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            Log::error('Notifications Fetch Failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve notifications.'
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        try {
            // Ensure user can only mark their own notifications as read
            if ($notification->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            if (is_null($notification->read_at)) {
                $notification->update(['read_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.'
            ]);

        } catch (\Exception $e) {
            Log::error('Mark Notification Read Failed', [
                'notification_id' => $notification->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read.'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $updatedCount = Notification::where('user_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => "Marked {$updatedCount} notifications as read."
            ]);

        } catch (\Exception $e) {
            Log::error('Mark All Notifications Read Failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read.'
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy(Notification $notification): JsonResponse
    {
        try {
            // Ensure user can only delete their own notifications
            if ($notification->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Notification Delete Failed', [
                'notification_id' => $notification->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification.'
            ], 500);
        }
    }

    /**
     * Create a test notification (for development)
     */
    public function createTest(): JsonResponse
    {
        try {
            $notification = Notification::create([
                'user_id' => Auth::id(),
                'type' => 'info',
                'title' => 'Test Notification',
                'message' => 'This is a test notification created at ' . now()->format('Y-m-d H:i:s'),
                'data' => json_encode(['test' => true])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test notification created.',
                'notification_id' => $notification->id
            ]);

        } catch (\Exception $e) {
            Log::error('Test Notification Creation Failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create test notification.'
            ], 500);
        }
    }
}
