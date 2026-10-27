<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use App\Support\PlatformContext;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn (Model $model) => static::writeAudit($model, 'created', null, $model->getAttributes()));

        static::updated(function (Model $model): void {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if ($changes !== []) {
                $before = array_intersect_key($model->getOriginal(), $changes);
                static::writeAudit($model, 'updated', $before, $changes);
            }
        });

        static::deleted(fn (Model $model) => static::writeAudit($model, 'deleted', $model->getOriginal(), null));
    }

    private static function writeAudit(Model $model, string $action, ?array $before, ?array $after): void
    {
        $actor = PlatformContext::user();

        AuditLog::query()->create([
            'auditable_type' => $model->getMorphClass(),
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'before' => $before,
            'after' => $after,
            'actor_email' => $actor->email,
            'actor_name' => $actor->name,
            'actor_external_id' => $actor->externalId,
            'ip_address' => app()->runningInConsole() ? null : request()->ip(),
            'user_agent' => app()->runningInConsole() ? null : request()->userAgent(),
        ]);
    }
}
