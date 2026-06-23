<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationLog extends Model
{
    protected $fillable = [
        'branch_id',
        'task_name',
        'status',
        'details',
        'idempotency_key',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
