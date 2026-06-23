<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'branch_id',
        'name',
        'quantity',
        'minimum_quantity',
        'unit',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
