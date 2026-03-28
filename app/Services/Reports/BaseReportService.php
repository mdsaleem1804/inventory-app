<?php

namespace App\Services\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class BaseReportService
{
    protected function normalizeFilters(array $filters): array
    {
        return collect($filters)
            ->mapWithKeys(function ($value, $key) {
                if ($value === null || $value === '') {
                    return [];
                }

                return [$key => $value];
            })
            ->all();
    }

    protected function applyCommonFilters(
        Builder $query,
        array $filters,
        ?string $dateColumn = null,
        ?string $productColumn = null,
        ?string $categoryColumn = null,
        ?string $fromDateKey = 'from_date',
        ?string $toDateKey = 'to_date'
    ): Builder {
        $filters = $this->normalizeFilters($filters);

        if ($dateColumn && ! empty($filters[$fromDateKey])) {
            $query->whereDate($dateColumn, '>=', $filters[$fromDateKey]);
        }

        if ($dateColumn && ! empty($filters[$toDateKey])) {
            $query->whereDate($dateColumn, '<=', $filters[$toDateKey]);
        }

        if ($productColumn && ! empty($filters['product_id'])) {
            $query->where($productColumn, $filters['product_id']);
        }

        if ($categoryColumn && ! empty($filters['category_id'])) {
            $query->where($categoryColumn, $filters['category_id']);
        }

        return $query;
    }

    public function structuredReportData(string $title, array $columns, iterable $rows, array $filters = []): array
    {
        return [
            'title' => $title,
            'columns' => $columns,
            'rows' => is_array($rows) ? $rows : collect($rows)->map(fn ($row) => is_array($row) ? $row : (array) $row)->all(),
            'filters' => $this->humanizeFilters($filters),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function humanizeFilters(array $filters): array
    {
        return collect($this->normalizeFilters($filters))
            ->mapWithKeys(function ($value, $key) {
                if (str_contains((string) $value, '-') && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value)) {
                    $value = Carbon::parse($value)->format('d M Y');
                }

                return [
                    str($key)->replace('_', ' ')->title()->toString() => $value,
                ];
            })
            ->all();
    }
}
