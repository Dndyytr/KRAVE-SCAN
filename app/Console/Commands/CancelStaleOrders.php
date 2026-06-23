<?php

namespace App\Console\Commands;

use App\Models\AutomationLog;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelStaleOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically cancel pending orders that have exceeded the 30-minute threshold';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = Carbon::now()->subMinutes(30);

        // Find pending orders older than 30 minutes across all branches
        $staleOrders = Order::withoutGlobalScopes()
            ->where('status', 'pending')
            ->where('created_at', '<', $threshold)
            ->get();

        $count = 0;

        foreach ($staleOrders as $order) {
            $order->update(['status' => 'cancelled']);

            AutomationLog::create([
                'branch_id' => $order->branch_id,
                'task_name' => 'Auto Cancel Order',
                'status' => 'success',
                'details' => json_encode([
                    'order_id' => $order->id,
                    'reason' => 'Stale order (> 30 minutes)',
                    'created_at' => $order->created_at->toDateTimeString(),
                ]),
            ]);

            $count++;
        }

        $this->info("Successfully cancelled {$count} stale pending orders.");
    }
}
