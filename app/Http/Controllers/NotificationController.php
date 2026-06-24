<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get recent notifications.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Get notifications for the authenticated user
        $notifications = $user->unreadNotifications()
            ->limit(10)
            ->get();

        // Map notifications to present standard format
        $data = $notifications->map(function ($notification) {
            $redirectUrl = '#';
            $type = $notification->data['type'] ?? 'general';

            if ($type === 'order_created' && isset($notification->data['order_id'])) {
                $redirectUrl = route('cashier.orders.show', $notification->data['order_id']);
            } elseif ($type === 'low_stock') {
                $redirectUrl = route('admin.stocks.index');
            } elseif ($type === 'daily_report') {
                $redirectUrl = route('admin.reports.sales');
            }

            return [
                'id' => $notification->id,
                'type' => $type,
                'message' => $notification->data['message'] ?? 'Notification received',
                'created_at' => $notification->created_at->diffForHumans(),
                'redirect_url' => $redirectUrl,
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $data,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
