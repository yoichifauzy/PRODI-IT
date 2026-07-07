<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityService extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'community_services';

    protected $fillable = [
        'document_id',
        'title',
        'location',
        'year',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
