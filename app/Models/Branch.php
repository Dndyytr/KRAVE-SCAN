<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'code', 'address', 'phone'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }

    public function salesReports()
    {
        return $this->hasMany(SalesReport::class);
    }

    public function financialReports()
    {
        return $this->hasMany(FinancialReport::class);
    }

    public function automationRules()
    {
        return $this->hasMany(AutomationRule::class);
    }

    protected static function booted(): void
    {
        static::created(function (Branch $branch) {
            $branch->automationRules()->createMany([
                [
                    'name' => 'Generate Receipt (Cetak Struk)',
                    'trigger_event' => 'App\Events\OrderPaid',
                    'condition_type' => 'always',
                    'condition_value' => null,
                    'action_job' => 'App\Jobs\GenerateReceiptJob',
                    'is_active' => true,
                ],
                [
                    'name' => 'Check Stock Levels (Cek Stok Rendah)',
                    'trigger_event' => 'App\Events\OrderPaid',
                    'condition_type' => 'always',
                    'condition_value' => null,
                    'action_job' => 'App\Jobs\CheckStockLevelsJob',
                    'is_active' => true,
                ],
            ]);
        });
    }
}
