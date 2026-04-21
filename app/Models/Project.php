<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'student_name',
        'student_nim',
        'year',
        'summary',
        'demo_url',
        'repository_url',
        'thumbnail',
        'status',
        'is_featured',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopeVisibleOnPublic(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function (\Illuminate\Database\Eloquent\Builder $visible): void {
            $visible
                ->where(function (\Illuminate\Database\Eloquent\Builder $published): void {
                    $published
                        ->where('status', 'published')
                        ->where(function (\Illuminate\Database\Eloquent\Builder $q): void {
                            $q->whereNull('published_at')->orWhere('published_at', '<=', now());
                        });
                })
                ->orWhere(function (\Illuminate\Database\Eloquent\Builder $scheduledDraft): void {
                    $scheduledDraft
                        ->where('status', 'draft')
                        ->whereNotNull('published_at')
                        ->where('published_at', '<=', now());
                });
        });
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
