<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningOutcome extends Model
{
    protected $table = 'learning_outcome';

    protected $fillable = [
        'code',
        'description',
        'created_by',
    ];
}
