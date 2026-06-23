<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
