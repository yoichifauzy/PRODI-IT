<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

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
