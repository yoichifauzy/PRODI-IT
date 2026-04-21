<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CplItem extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'cpl_category_id',
        'code',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CplCategory::class, 'cpl_category_id');
    }
}
