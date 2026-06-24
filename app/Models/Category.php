<?php

namespace App\Models;

use App\Traits\LogsActivity;
use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use LogsActivity, ScopedToBranch;

    protected $fillable = ['name', 'branch_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
