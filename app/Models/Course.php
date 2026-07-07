<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'semester',
        'major_selection',
        'code',
        'name',
        'credits_theory',
        'credits_practice',
        'created_by',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(AssetLink::class, 'document_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
