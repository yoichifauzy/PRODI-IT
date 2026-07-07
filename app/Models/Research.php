<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'researches';

    protected $fillable = [
        'document_id',
        'title',
        'researcher_name',
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
