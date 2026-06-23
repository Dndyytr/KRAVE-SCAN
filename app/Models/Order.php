<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'branch_id',
        'table_number',
        'status',
        'total_amount',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class);
    }
}
