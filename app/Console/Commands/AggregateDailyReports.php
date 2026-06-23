<?php

namespace App\Console\Commands;

use App\Models\AutomationLog;
use App\Models\Branch;
use App\Models\FinancialReport;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SalesReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateDailyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:aggregate-daily {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically aggregates sales and financial reports for each branch for the previous day (or a specific date)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Default to yesterday if no date is passed
        $dateParam = $this->argument('date');
        $date = $dateParam ? Carbon::parse($dateParam) : Carbon::yesterday();
        $dateString = $date->format('Y-m-d');

        $this->info("Starting report aggregation for date: {$dateString}");

        $branches = Branch::all();
        $count = 0;

        foreach ($branches as $branch) {
            try {
                DB::beginTransaction();

                // 1. Calculate Sales Data
                // Total completed/confirmed orders created on the target date
                $totalOrders = Order::withoutGlobalScopes()
                    ->where('branch_id', $branch->id)
                    ->whereIn('status', ['confirmed', 'in_process', 'completed'])
                    ->whereDate('created_at', $dateString)
                    ->count();

                // Total revenue from successful payments created on the target date
                $totalRevenue = Payment::withoutGlobalScopes()
                    ->join('orders', 'payments.order_id', '=', 'orders.id')
                    ->where('orders.branch_id', $branch->id)
                    ->where('payments.status', 'success')
                    ->whereDate('payments.created_at', $dateString)
                    ->sum('payments.amount');

                // 2. Create or Update Sales Report
                $salesReport = SalesReport::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'date' => $dateString,
                    ],
                    [
                        'total_orders' => $totalOrders,
                        'total_revenue' => $totalRevenue,
                    ]
                );

                // 3. Create or Update Financial Report for Income
                if ($totalRevenue > 0) {
                    FinancialReport::updateOrCreate(
                        [
                            'branch_id' => $branch->id,
                            'date' => $dateString,
                            'type' => 'income',
                            'description' => "Daily Revenue Aggregation for {$dateString}",
                        ],
                        [
                            'amount' => $totalRevenue,
                        ]
                    );
                }

                // Log RPA success
                AutomationLog::create([
                    'branch_id' => $branch->id,
                    'task_name' => 'Aggregate Daily Reports',
                    'status' => 'success',
                    'details' => json_encode([
                        'date' => $dateString,
                        'total_orders' => $totalOrders,
                        'total_revenue' => $totalRevenue,
                        'sales_report_id' => $salesReport->id,
                    ]),
                ]);

                DB::commit();
                $count++;

            } catch (\Exception $e) {
                DB::rollBack();

                AutomationLog::create([
                    'branch_id' => $branch->id,
                    'task_name' => 'Aggregate Daily Reports',
                    'status' => 'failed',
                    'details' => json_encode([
                        'date' => $dateString,
                        'error' => $e->getMessage(),
                    ]),
                ]);

                $this->error("Failed to aggregate reports for branch {$branch->name}: ".$e->getMessage());
            }
        }

        $this->info("Successfully aggregated reports for {$count} branches.");
    }
}
