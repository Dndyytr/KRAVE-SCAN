<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderCreatedNotifications implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        // Find all staff (admin & cashier) in the same branch, plus Super Admins (branch_id = null)
        $users = User::withoutGlobalScopes()
            ->where(function ($query) use ($order) {
                $query->where('branch_id', $order->branch_id)
                    ->orWhereNull('branch_id');
            })
            ->where('is_active', true)
            ->get();

        Notification::send($users, new OrderCreatedNotification($order));
    }
}
