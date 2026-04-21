<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'category',
        'title',
        'description',
        'location',
        'event_date',
        'published_at',
        'image_path',
        'sort_order',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function scopeVisibleOnPublic(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (\Illuminate\Database\Eloquent\Builder $q): void {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
