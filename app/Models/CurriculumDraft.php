<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumDraft extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $table = 'curricula_drafts';

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function courses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CurriculumCourseDraft::class, 'curriculum_draft_id')->orderBy('code')->orderBy('name');
    }
}
