<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\EmployeeCollection;
use App\Models\EducationLevel;
use App\Models\Employee;
use App\Traits\DataTables;
use App\Traits\QueryBuilder;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use QueryBuilder;
    use DataTables;

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->queryBuilder(
                model: new Employee,
                columns: ['*'],
                relations: ['position', 'department', 'educationLevel', 'employmentStatus', 'contract.contractType']
            );

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->addColumn('position', fn($row) => $row->position ? $row->position->name : 'NA')
                    ->addColumn('education_level', fn($row) => $row->educationLevel ? $row->educationLevel->name : 'NA')
                    ->addColumn('contract_code', fn($row) => $row->contract ? $row->contract->code : "Không xác định")
                    ->addColumn('contract_type', fn($row) => $row->contract ? $row->contract->contractType->name : "Không xác định")
                    ->addColumn('contract_link', fn($row) => $row->contract ? "<a href=''>Hợp đồng lao động</a>" : "Không xác định")
                    ->addColumn('employment_status', fn($row) => $row->employmentStatus ? $row->employmentStatus->name : 'NA')
                    ->addColumn('age', fn($row) => $row->age ?? 'NA')
                    ->addColumn('days_left_for_university', fn($row) => $row->days_left_for_university ?? '<span class="text-muted">Chưa có</span>')
                    ->addColumn('full_name_code', fn($row) => $row->nameCode)
                    ->addColumn('seniority', fn($row) => $row->seniority)
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                    ->editColumn('operations', fn($row) => view('components.operation', ['row' => $row])->render()),
                ['days_left_for_university', 'contract_link']

            );
        }

        return view('backend.employee.index');
    }
}
