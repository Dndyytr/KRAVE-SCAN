<?php

namespace Database\Seeders;

use App\Models\AutomationRule;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class AutomationRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();

        foreach ($branches as $branch) {
            if ($branch->automationRules()->count() > 0) {
                continue;
            }

            // Rule 1: Generate Receipt
            AutomationRule::create([
                'branch_id' => $branch->id,
                'name' => 'Generate Receipt (Cetak Struk)',
                'trigger_event' => 'App\Events\OrderPaid',
                'condition_type' => 'always',
                'condition_value' => null,
                'action_job' => 'App\Jobs\GenerateReceiptJob',
                'is_active' => true,
            ]);

            // Rule 2: Check Stock Levels
            AutomationRule::create([
                'branch_id' => $branch->id,
                'name' => 'Check Stock Levels (Cek Stok Rendah)',
                'trigger_event' => 'App\Events\OrderPaid',
                'condition_type' => 'always',
                'condition_value' => null,
                'action_job' => 'App\Jobs\CheckStockLevelsJob',
                'is_active' => true,
            ]);
        }
    }
}
