<?php

namespace App\Traits;

use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

// use Illuminate\Database\Eloquent\Builder;

trait DataTables
{

    public function processDataTable(
        $query,
        ?callable $customizeColumns = null,
        array $rawColumn = [],
        bool $includeDefaultRawColumns = true
    ) {
        $dataTable = FacadesDataTables::eloquent($query);

        if ($customizeColumns) {
            $dataTable = $customizeColumns($dataTable);
        }

        if ($includeDefaultRawColumns) {
            $rawColumn = array_merge($rawColumn, ['checkbox', 'operations']);
        }

        $rawColumn = array_unique(array_filter($rawColumn));
        $dataTable->rawColumns($rawColumn);

        return $dataTable
            ->addColumn('checkbox', fn($row) => "<input type='checkbox' class='row-checkbox form-check-input' value='{$row->id}'>")
            ->addIndexColumn()
            ->make(true);
    }
}
