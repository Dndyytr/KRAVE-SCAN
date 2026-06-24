<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $branchName = $this->order->branch ? $this->order->branch->name : 'Unknown Branch';

        return (new MailMessage)
            ->subject('Pesanan Baru Masuk - '.$branchName)
            ->greeting('Halo, '.$notifiable->name)
            ->line('Ada pesanan baru masuk di cabang '.$branchName.'.')
            ->line('ID Pesanan: '.$this->order->id)
            ->line('Nomor Meja: '.$this->order->table_number)
            ->line('Total: Rp '.number_format($this->order->total_amount, 0, ',', '.'))
            ->action('Lihat Detail Pesanan', route('cashier.orders.show', $this->order->id))
            ->line('Terima kasih telah menggunakan sistem kami!');
    }

    public function toArray($notifiable): array
    {
        $branchName = $this->order->branch ? $this->order->branch->name : 'Unknown Branch';

        return [
            'type' => 'order_created',
            'order_id' => $this->order->id,
            'branch_name' => $branchName,
            'table_number' => $this->order->table_number,
            'total_amount' => $this->order->total_amount,
            'message' => 'Pesanan baru #'.$this->order->id.' telah dibuat di Meja '.$this->order->table_number.'.',
        ];
    }
}
