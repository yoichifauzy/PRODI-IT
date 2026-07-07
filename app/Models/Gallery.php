<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'category',
        'year',
        'image_path',
        'position',
        'created_by',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    /**
     * URL lengkap ke gambar dari storage.
     */
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
