<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIImageSearchLog extends Model
{
    protected $fillable = [
        'image_path',
        'matched_menu_id',
        'confidence_score',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'matched_menu_id');
    }
}
