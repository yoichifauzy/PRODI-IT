<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'sort_order',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }
}
