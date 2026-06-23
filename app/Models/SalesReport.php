<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'branch_id',
        'date',
        'total_orders',
        'total_revenue',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
