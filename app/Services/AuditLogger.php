<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public static function log(string $action, ?Model $target = null, array $before = null, array $after = null, array $meta = [], ?string $actorId = null)
    {
        AuditLog::create([
            'actor_id' => $actorId ?? auth()->id(),
            'action' => $action,

            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target ? (string) $target->getKey() : null,

            'before' => $before,
            'after' => $after,
            'meta' => $meta,

            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
