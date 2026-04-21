<?php

namespace App\Models;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'gallery_id',
        'title',
        'caption',
        'image_path',
        'taken_at',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function scopeVisibleOnPublic(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function (\Illuminate\Database\Eloquent\Builder $q): void {
            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
        });
    }

    public function gallery(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }
}
