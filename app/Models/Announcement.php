<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
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

    /**
     * @return array{signature: string, count: int, latestId: int|null, latestUpdatedAt: string|null}
     */
    public static function publishedSyncPayload(): array
    {
        $query = static::query()->published();

        $count = (clone $query)->count();
        $latestId = (clone $query)->max('id');
        $latestUpdatedAt = (clone $query)->max('updated_at');

        $signatureBase = implode('|', [
            (string) $count,
            $latestId !== null ? (string) $latestId : '',
            $latestUpdatedAt !== null ? (string) $latestUpdatedAt : '',
        ]);

        return [
            'signature' => sha1($signatureBase),
            'count' => $count,
            'latestId' => $latestId !== null ? (int) $latestId : null,
            'latestUpdatedAt' => $latestUpdatedAt !== null ? (string) $latestUpdatedAt : null,
        ];
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
