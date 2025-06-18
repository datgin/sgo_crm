<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\EmployeeCollection;
use App\Models\Department;
use App\Models\EducationLevel;
use App\Models\Employee;
use App\Traits\DataTables;
use App\Traits\QueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                fn ($dataTable) =>
                $dataTable
                    ->addColumn('position', fn ($row) => $row->position ? $row->position->name : 'NA')
                    ->addColumn('education_level', fn ($row) => $row->educationLevel ? $row->educationLevel->name : 'NA')
                    ->addColumn('contract_code', fn ($row) => $row->contract ? $row->contract->code : "Không xác định")
                    ->addColumn('contract_type', fn ($row) => $row->contract ? $row->contract->contractType->name : "Không xác định")
                    ->addColumn('contract_link', fn ($row) => $row->contract ? "<a href=''>Hợp đồng lao động</a>" : "Không xác định")
                    ->addColumn('employment_status', fn ($row) => $row->employmentStatus ? $row->employmentStatus->name : 'NA')
                    ->addColumn('age', fn ($row) => $row->age ?? 'NA')
                    ->addColumn('days_left_for_university', fn ($row) => $row->days_left_for_university ?? '<span class="text-muted">Chưa có</span>')
                    ->addColumn('full_name_code', fn ($row) => $row->nameCode)
                    ->addColumn('seniority', fn ($row) => $row->seniority)
                    ->editColumn('created_at', fn ($row) => $row->created_at->format('d-m-Y'))
                    ->editColumn('operations', fn ($row) => view('components.operation', ['row' => $row])->render()),
                ['days_left_for_university', 'contract_link']

            );
        }

        return view('backend.employee.index');
    }


    public function view($id, Request $request)
    {
        try {
            $employees = Employee::all();
            $employeeId = $request->input('id', $id);

            $employee = Employee::with([
                'contract.contractType',
                'department',
                'position',
                'employmentStatus',
                'educationLevel',
            ])->findOrFail($employeeId);

            if ($request->ajax()) {
                return response()->json([
                    'avatar'             => asset($employee->avatar),
                    'full_name'          => $employee->full_name,
                    'gender_text'        => $employee->gender_text,
                    'age'                => \Carbon\Carbon::parse($employee->birthday)->age . ' tuổi',
                    'department'         => $employee->department->name,
                    'position'           => $employee->position->name,
                    'birthday'           => \Carbon\Carbon::parse($employee->birthday)->format('d/m/Y'),
                    'phone'              => $employee->phone,
                    'cccd'               => $employee->cccd,
                    'code'               => $employee->code,
                    'address'            => $employee->address,
                    'start_date'         => \Carbon\Carbon::parse($employee->contract->start_date)->format('d/m/Y'),
                    'end_date'           => \Carbon\Carbon::parse($employee->contract->end_date)->format('d/m/Y'),
                    'seniority_detail'   => $employee->seniority_detail,
                    'contract_type'      => $employee->contract->contractType->name,
                    'employment_status'  => $employee->employmentStatus->name,
                    'resignation_date'   => $employee->resignation_date
                        ? \Carbon\Carbon::parse($employee->resignation_date)->format('d/m/Y')
                        : '',
                    'notes'              => $employee->notes,
                    'education_level'    => $employee->educationLevel->name,
                ]);
            }
            return view('backend.employee.view', compact('employees', 'employee'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'Nhân viên không tồn tại');
        }
    }

    public function information(Request $request)
    {
        Log::info($request->all());
        $departments = Department::all();

        $employees = Employee::with(['position', 'department', 'employmentStatus']);

        if ($request->filled('department') && $request->department != 0) {
            $employees->where('department_id', $request->department);
        }

        $employees = $employees->get();

        // Đếm nhân sự đang làm việc
        $totalEmployeeCount = $employees->count();
        $activeEmployeeCount = $employees->where('employment_status_id', 2)->count();

        if ($request->ajax()) {
            $html = view('backend.employee.partials.employee_list', compact('employees', 'activeEmployeeCount'))->render();
            return response()->json([
                'html' => $html,
                'total_count' => $totalEmployeeCount,
                'active_count' => $activeEmployeeCount
            ]);
        }

        return view('backend.employee.information', compact('departments', 'employees', 'activeEmployeeCount'));
    }
}
