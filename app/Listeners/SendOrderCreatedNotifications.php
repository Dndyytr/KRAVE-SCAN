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

        // Find all staff in the same branch
        $users = User::withoutGlobalScopes()
            ->where('branch_id', $order->branch_id)
            ->where('is_active', true)
            ->get();

        Notification::send($users, new OrderCreatedNotification($order));
    }
}
