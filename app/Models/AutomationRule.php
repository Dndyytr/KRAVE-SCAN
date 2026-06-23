<?php

namespace App\Models;

use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use ScopedToBranch;

    protected $fillable = [
        'branch_id',
        'name',
        'trigger_event',
        'condition_type',
        'condition_value',
        'action_job',
        'is_active',
    ];

    protected $casts = [
        'condition_value' => 'array',
        'is_active' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
