<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'payment_id',
        'receipt_number',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
