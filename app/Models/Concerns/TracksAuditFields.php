<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait TracksAuditFields
{
    protected static array $auditColumnCache = [];

    public static function bootTracksAuditFields(): void
    {
        static::creating(function ($model): void {
            $userId = Auth::id();

            if (! $userId) {
                return;
            }

            if ($model->hasAuditColumn('created_by') && ! $model->created_by) {
                $model->created_by = $userId;
            }

            if ($model->hasAuditColumn('updated_by') && ! $model->updated_by) {
                $model->updated_by = $userId;
            }
        });

        static::updating(function ($model): void {
            $userId = Auth::id();

            if (! $userId) {
                return;
            }

            if ($model->hasAuditColumn('updated_by')) {
                $model->updated_by = $userId;
            }
        });

        static::deleting(function ($model): void {
            $userId = Auth::id();

            if (! $userId || ! $model->hasAuditColumn('deleted_by')) {
                return;
            }

            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            $model->newModelQuery()->whereKey($model->getKey())->update([
                'deleted_by' => $userId,
                'updated_at' => now(),
            ]);
        });
    }

    protected function hasAuditColumn(string $column): bool
    {
        $table = $this->getTable();

        if (! Schema::hasTable($table)) {
            return false;
        }

        if (! array_key_exists($table, self::$auditColumnCache)) {
            self::$auditColumnCache[$table] = Schema::getColumnListing($table);
        }

        return in_array($column, self::$auditColumnCache[$table], true);
    }
}
