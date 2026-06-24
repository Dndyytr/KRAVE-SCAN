<?php

namespace App\Models;

use App\Traits\LogsActivity;
use App\Traits\ScopedToBranch;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use LogsActivity, ScopedToBranch;

    protected $fillable = [
        'branch_id',
        'category_id',
        'stock_item_id',
        'name',
        'description',
        'price',
        'image_path',
        'is_active',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function aiImageSearchLogs()
    {
        return $this->hasMany(AIImageSearchLog::class, 'matched_menu_id');
    }
}
