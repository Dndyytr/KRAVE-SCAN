<?php

namespace App\Jobs;

use App\Models\AutomationLog;
use App\Models\Order;
use App\Models\StockItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckStockLevelsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Order $order;

    protected ?int $automationLogId;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, ?int $automationLogId = null)
    {
        $this->order = $order;
        $this->automationLogId = $automationLogId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        $order->load('orderItems.menu');

        $lowStockItems = [];

        foreach ($order->orderItems as $item) {
            if ($item->menu && $item->menu->stock_item_id) {
                $stockItem = StockItem::withoutGlobalScopes()
                    ->where('id', $item->menu->stock_item_id)
                    ->first();

                if ($stockItem && $stockItem->quantity <= $stockItem->minimum_quantity) {
                    $lowStockItems[] = [
                        'stock_item_id' => $stockItem->id,
                        'name' => $stockItem->name,
                        'current_quantity' => $stockItem->quantity,
                        'minimum_quantity' => $stockItem->minimum_quantity,
                        'unit' => $stockItem->unit,
                    ];
                }
            }
        }

        if (! empty($lowStockItems)) {
            $warningDetails = [
                'order_id' => $order->id,
                'low_stock_items' => $lowStockItems,
            ];

            if ($this->automationLogId) {
                $log = AutomationLog::withoutGlobalScopes()->find($this->automationLogId);
                if ($log) {
                    $log->update([
                        'status' => 'warning',
                        'details' => array_merge($log->details ?? [], $warningDetails),
                    ]);
                }
            } else {
                AutomationLog::create([
                    'branch_id' => $order->branch_id,
                    'task_name' => 'Low Stock Warning',
                    'status' => 'warning',
                    'details' => json_encode($warningDetails),
                ]);
            }
        } else {
            // Update log to success if no items are low stock
            if ($this->automationLogId) {
                $log = AutomationLog::withoutGlobalScopes()->find($this->automationLogId);
                if ($log) {
                    $log->update([
                        'status' => 'success',
                        'details' => array_merge($log->details ?? [], [
                            'order_id' => $order->id,
                            'message' => 'All items in this order have sufficient stock levels.',
                        ]),
                    ]);
                }
            }
        }
    }
}
