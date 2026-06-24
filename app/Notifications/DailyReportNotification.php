<?php

namespace App\Notifications;

use App\Models\SalesReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $salesReport;

    public function __construct(SalesReport $salesReport)
    {
        $this->salesReport = $salesReport;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $branchName = $this->salesReport->branch ? $this->salesReport->branch->name : 'Unknown Branch';

        return (new MailMessage)
            ->subject('Laporan Penjualan Harian - '.$branchName)
            ->greeting('Halo, '.$notifiable->name)
            ->line('Laporan penjualan harian untuk cabang '.$branchName.' pada tanggal '.$this->salesReport->date.' telah selesai dienkapsulasi.')
            ->line('Total Pesanan: '.$this->salesReport->total_orders)
            ->line('Total Pendapatan: Rp '.number_format($this->salesReport->total_revenue, 0, ',', '.'))
            ->action('Lihat Laporan Penjualan', route('admin.reports.sales'))
            ->line('Terima kasih telah menggunakan sistem kami!');
    }

    public function toArray($notifiable): array
    {
        $branchName = $this->salesReport->branch ? $this->salesReport->branch->name : 'Unknown Branch';

        return [
            'type' => 'daily_report',
            'sales_report_id' => $this->salesReport->id,
            'date' => $this->salesReport->date,
            'total_orders' => $this->salesReport->total_orders,
            'total_revenue' => $this->salesReport->total_revenue,
            'branch_name' => $branchName,
            'message' => 'Laporan penjualan harian cabang '.$branchName.' untuk tanggal '.$this->salesReport->date.' telah siap.',
        ];
    }
}
