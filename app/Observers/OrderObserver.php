<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->histories()->create([
            'status' => $order->status,
            'user_id' => Auth::id(),
            'notes' => __('Pesanan berhasil dibuat.'),
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $notes = $this->getStatusChangeNotes($order->status);
            $order->histories()->create([
                'status' => $order->status,
                'user_id' => Auth::id(),
                'notes' => $notes,
            ]);
        }
    }

    /**
     * Get descriptive notes for status changes.
     */
    protected function getStatusChangeNotes(string $status): string
    {
        return match ($status) {
            'pending' => __('Pesanan dalam antrean pembayaran.'),
            'confirmed' => __('Pembayaran dikonfirmasi, pesanan diteruskan ke dapur.'),
            'in_process' => __('Pesanan sedang diproses di dapur.'),
            'completed' => __('Pesanan selesai dan disajikan.'),
            'cancelled' => __('Pesanan dibatalkan.'),
            default => __("Status pesanan diperbarui menjadi {$status}."),
        };
    }
}
