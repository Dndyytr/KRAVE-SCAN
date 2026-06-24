<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'user_id',
        'branch_id',
        'action',
        'loggable_type',
        'loggable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function loggable()
    {
        return $this->morphTo();
    }
}
