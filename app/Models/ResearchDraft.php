<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchDraft extends Model
{
    protected $table = 'researches_drafts';

    protected $fillable = [
        'title',
        'researcher_name',
        'researcher_role',
        'year',
        'publication',
        'link',
        'abstract',
        'status',
        'created_by',
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
