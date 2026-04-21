<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspiration extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'nim',
        'subject',
        'message',
        'status',
        'read_at',
        'read_by',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function reader(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function markAsRead(User $user): void
    {
        if ($this->status !== 'read') {
            $this->forceFill([
                'status' => 'read',
                'read_at' => now(),
                'read_by' => $user->id,
            ])->save();
        }
    }
}
