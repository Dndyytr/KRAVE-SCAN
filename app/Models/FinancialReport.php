<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'branch_id',
        'date',
        'type',
        'amount',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
