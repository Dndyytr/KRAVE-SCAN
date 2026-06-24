<?php

namespace App\Notifications;

use App\Models\StockItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $stockItem;

    public function __construct(StockItem $stockItem)
    {
        $this->stockItem = $stockItem;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $branchName = $this->stockItem->branch ? $this->stockItem->branch->name : 'Unknown Branch';

        return (new MailMessage)
            ->subject('Peringatan Stok Menipis - '.$branchName)
            ->greeting('Halo, '.$notifiable->name)
            ->line('Stok untuk item berikut telah berada di bawah batas minimum di cabang '.$branchName.'.')
            ->line('Item: '.$this->stockItem->name)
            ->line('Stok Saat Ini: '.$this->stockItem->quantity)
            ->action('Kelola Stok', route('admin.stocks.index'))
            ->line('Segera lakukan pengisian ulang untuk menghindari kehabisan stok.');
    }

    public function toArray($notifiable): array
    {
        $branchName = $this->stockItem->branch ? $this->stockItem->branch->name : 'Unknown Branch';

        return [
            'type' => 'low_stock',
            'stock_item_id' => $this->stockItem->id,
            'item_name' => $this->stockItem->name,
            'quantity' => $this->stockItem->quantity,
            'branch_name' => $branchName,
            'message' => 'Peringatan: Stok item '.$this->stockItem->name.' menipis (Sisa: '.$this->stockItem->quantity.').',
        ];
    }
}
