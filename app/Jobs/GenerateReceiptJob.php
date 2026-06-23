<?php

namespace App\Jobs;

use App\Models\AutomationLog;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateReceiptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Payment $payment;

    protected ?int $automationLogId;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment, ?int $automationLogId = null)
    {
        $this->payment = $payment;
        $this->automationLogId = $automationLogId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payment = $this->payment;
        $order = $payment->order;

        if (! $order) {
            if ($this->automationLogId) {
                $log = AutomationLog::withoutGlobalScopes()->find($this->automationLogId);
                if ($log) {
                    $log->update([
                        'status' => 'failed',
                        'details' => array_merge($log->details ?? [], ['error' => 'Order not found for payment ID: '.$payment->id]),
                    ]);
                }
            } else {
                AutomationLog::create([
                    'task_name' => 'Generate Receipt',
                    'status' => 'failed',
                    'details' => json_encode(['error' => 'Order not found for payment ID: '.$payment->id]),
                ]);
            }

            return;
        }

        // Check if receipt already exists
        if ($payment->receipts()->exists()) {
            return; // Already generated
        }

        try {
            DB::beginTransaction();

            $branchCode = $order->branch ? $order->branch->code : 'HQ';
            $date = now()->format('Ymd');
            $paddedPaymentId = str_pad($payment->id, 4, '0', STR_PAD_LEFT);
            $receiptNumber = "REC-{$branchCode}-{$date}-{$paddedPaymentId}";

            $receipt = Receipt::create([
                'payment_id' => $payment->id,
                'receipt_number' => $receiptNumber,
                'printed_at' => now(),
            ]);

            $successDetails = [
                'receipt_id' => $receipt->id,
                'receipt_number' => $receiptNumber,
                'payment_id' => $payment->id,
                'order_id' => $order->id,
            ];

            if ($this->automationLogId) {
                $log = AutomationLog::withoutGlobalScopes()->find($this->automationLogId);
                if ($log) {
                    $log->update([
                        'status' => 'success',
                        'details' => array_merge($log->details ?? [], $successDetails),
                    ]);
                }
            } else {
                AutomationLog::create([
                    'branch_id' => $order->branch_id,
                    'task_name' => 'Generate Receipt',
                    'status' => 'success',
                    'details' => json_encode($successDetails),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $failedDetails = [
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ];

            if ($this->automationLogId) {
                $log = AutomationLog::withoutGlobalScopes()->find($this->automationLogId);
                if ($log) {
                    $log->update([
                        'status' => 'failed',
                        'details' => array_merge($log->details ?? [], $failedDetails),
                    ]);
                }
            } else {
                AutomationLog::create([
                    'branch_id' => $order->branch_id,
                    'task_name' => 'Generate Receipt',
                    'status' => 'failed',
                    'details' => json_encode($failedDetails),
                ]);
            }

            throw $e;
        }
    }
}
