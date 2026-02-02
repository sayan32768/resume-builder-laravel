<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'action',
        'target_type',
        'target_id',
        'meta',
        'before',
        'after',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'meta' => 'array',
        'before' => 'array',
        'after' => 'array',
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
