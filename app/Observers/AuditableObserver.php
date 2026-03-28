<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuditableObserver
{
    public function created(Model $model): void
    {
        $this->log($model, 'CREATE', null, $this->sanitize($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        $oldValues = [];

        foreach (array_keys($changes) as $field) {
            $oldValues[$field] = $model->getOriginal($field);
        }

        $newValues = [];

        foreach (array_keys($changes) as $field) {
            $newValues[$field] = $model->getAttribute($field);
        }

        $this->log($model, 'UPDATE', $this->sanitize($oldValues), $this->sanitize($newValues));
    }

    public function deleted(Model $model): void
    {
        $this->log($model, 'DELETE', $this->sanitize($model->getOriginal()), null);
    }

    private function log(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        if (! Schema::hasTable('audit_logs') || $model instanceof AuditLog) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => $model->getTable(),
            'record_id' => (string) $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    private function sanitize(array $values): array
    {
        $sanitized = [];

        foreach ($values as $key => $value) {
            if (is_resource($value)) {
                continue;
            }

            if (is_scalar($value) || $value === null) {
                $sanitized[$key] = $value;
                continue;
            }

            $sanitized[$key] = json_decode(json_encode($value), true);
        }

        return $sanitized;
    }
}
