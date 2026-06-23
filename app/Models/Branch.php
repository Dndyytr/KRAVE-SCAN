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
}
