<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'is_published',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
