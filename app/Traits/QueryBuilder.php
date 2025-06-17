<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait QueryBuilder
{
    public function queryBuilder(
        $model,
        array $columns = ['*'],
        array $relations = [],
        array $filters = [],
        array $wheres = [],
        array $withCount = [],
        array $order = [],
        string $dateColumn = 'created_at',
        array $conditions = [],
        bool  $all = false
    ) {
        $conditions = $conditions ?: request()->all();

        $query = $model->select($columns);

        if (!empty($relations)) {
            $query->with($relations);
        }

        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        // Apply where nâng cao
        foreach ($wheres as $where) {
            $column  = $where['column'] ?? null;
            $operator = $where['operator'] ?? '=';
            $value   = $where['value'] ?? null;
            $method  = $where['method'] ?? 'where';

            if (!$column) continue;

            match ($method) {
                'whereIn' => $query->whereIn($column, (array) $value),
                'whereNotIn' => $query->whereNotIn($column, (array) $value),
                'whereNull' => $query->whereNull($column),
                'whereNotNull' => $query->whereNotNull($column),
                'whereBetween' => $query->whereBetween($column, (array) $value),
                'whereDate' => $query->whereDate($column, $operator, $value),
                default => $query->where($column, $operator, $value),
            };
        }

        // Filter theo key-value
        foreach ($filters as $filter) {
            $query->when(
                !empty($conditions[$filter]),
                fn($q) => $q->where($filter, $conditions[$filter])
            );
        }

        // Date range
        if (!empty($conditions['start_date']) && !empty($conditions['end_date'])) {
            $query->whereBetween($dateColumn, [
                $conditions['start_date'],
                $conditions['end_date'] . ' 23:59:59',
            ]);
        }

        // Sắp xếp
        if (!empty($order)) {
            $query->orderBy($order[0], $order[1]);
        } else {
            $query->latest('id');
        }

        Log::info('[QUERY]', ['sql' => $query->toRawSql()]);

        return $all ? $query->get() : $query;
    }
}
