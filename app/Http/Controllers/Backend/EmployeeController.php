<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\ContractType;
use App\Models\Department;
use App\Models\EducationLevel;
use App\Models\Employee;
use App\Models\EmploymentStatus;
use App\Models\Position;
use App\Models\User;
use App\Traits\DataTables;
use App\Traits\QueryBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;


class EmployeeController extends Controller
{
    use QueryBuilder;
    use DataTables;

    public function index(Request $request)
    {
        $this->authorize('view', Employee::class);

        if ($request->ajax()) {
            $query = $this->queryBuilder(
                model: new Employee,
                columns: ['*'],
                relations: ['position', 'department', 'educationLevel', 'employmentStatus', 'latestContract.contractType']
            );

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->addColumn('position', fn($row) => $row->position ? $row->position->name : '<small class="text-muted">Chưa cập nhật...</small>')
                    ->addColumn('education_level', fn($row) => $row->educationLevel ? $row->educationLevel->name : '<small class="text-muted">Chưa cập nhật...</small>')
                    ->addColumn('contract_code', fn($row) => $row->latestContract ? $row->latestContract->code : '<small class="text-muted">Chưa cập nhật...</small>')
                    ->addColumn('contract_type', fn($row) => $row->latestContract ? $row->latestContract->contractType->name : '<small class="text-muted">Chưa cập nhật...</small>')
                    ->addColumn('contract_link', fn($row) => $row->latestContract ? "<a target='_blank' href='" . showImage($row->latestContract->file_url) . "'>Hợp đồng lao động</a>" : '<small class="text-muted">Chưa cập nhật...</small>')
                    ->addColumn('employment_status', fn($row) => $row->employmentStatus ? $row->employmentStatus->name : '<small class="text-muted">Chưa cập nhật...</small>')
                    ->addColumn('age', fn($row) => $row->age)
                    ->addColumn('seniority', fn($row) => $row->seniority)
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                    ->editColumn('status', fn($row) => view('components.switch-checkbox', ['checked' => $row->status, 'id' => $row->id])->render())
                    ->editColumn('operations', fn($row) => view('components.operation', ['row' => $row])->render()),
                ['contract_link', 'age', 'status', 'contract_code', 'contract_type', 'position', 'education_level', 'employment_status', 'seniority']

            );
        }

        return view('backend.employee.index');
    }

    public function view($id, Request $request)
    {

        try {
            $employees = Employee::query()->pluck('full_name', 'id')->toArray();
            $employeeId = $request->input('id', $id);

            $employee = Employee::with([
                'latestContract.contractType',
                'department',
                'position',
                'employmentStatus',
                'educationLevel',
            ])->findOrFail($employeeId);

            if ($request->ajax()) {
                return response()->json([
                    'avatar'             => $employee->avatar,
                    'full_name'          => $employee->full_name,
                    'gender_text'        => $employee->gender_text,
                    'age'                => $employee->birthday->age . ' tuổi',
                    'department'         => $employee->department->name,
                    'position'           => $employee->position->name,
                    'birthday'           => $employee->birthday ? $employee->birthday->format('d/m/Y') : "<span class='text-muted'>Chưa xác định</span>",
                    'phone'              => $employee->phone,
                    'cccd'               => $employee->cccd,
                    'code'               => $employee->code,
                    'address'            => $employee->address,
                    'start_date'         => $employee->startDate,
                    'end_date'           => $employee->endDate,
                    'seniority_detail'   => $employee->seniority_detail,
                    'contract_type'      => $employee->contractType,
                    'employment_status'  => $employee->employmentStatus->name,
                    'resignation_date'   => $employee->resignation_date ? $employee->resignation_date->format('d/m/Y') : "<span class='text-muted'>Chưa xác định</span>",
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

    public function save(?string $id = null)
    {
        $this->authorize('create', Employee::class);

        $title          = "Tạo mới nhân viên";
        $employee       = null;
        $assignedPermissions = [];

        $positions = Position::query()->pluck('name', 'id')->toArray();
        $departments = Department::query()->pluck('name', 'id')->toArray();
        $educationLevels = EducationLevel::query()->pluck('name', 'id')->toArray();
        $employeeStatuses = EmploymentStatus::query()->pluck('name', 'id')->toArray();
        $contractTypes = ContractType::query()->pluck('name', 'id')->toArray();
        $permissions = $this->groupPermissionsByNamespace();

        if (!empty($id)) {
            $this->authorize('edit', Employee::class);

            $employee   = Employee::query()->with(['latestContract', 'user'])->findOrFail($id);
            $assignedPermissions = $employee->user->permissions->pluck('name')->toArray();
            $title      = "Chỉnh sửa nhân viên - {$employee->full_name}";
        }

        return view('backend.employee.save', compact('title', 'employee', 'positions', 'departments', 'educationLevels', 'employeeStatuses', 'contractTypes', 'permissions', 'assignedPermissions'));
    }

    public function store(EmployeeRequest $request)
    {
        
        $this->authorize('create', Employee::class);

        return transaction(function () use ($request) {
            $credentials = $request->validated();
            $credentials['code'] ??= $this->generateEmployeeCode();
            $credentials['password'] = bcrypt($credentials['password']);

            // Tạo nhân viên
            $employee = Employee::create($credentials);

            // Tạo user liên kết
            $user = User::create([
                'employee_id' => $employee->id,
                'name' => $employee->full_name,
                'email' => $employee->email,
                'avatar' => $credentials['avatar'],
                'phone' => $employee->phone,
                'password' => $credentials['password'],
            ]);

            $user->syncPermissions($credentials['permissions']);

            // Thêm hợp đồng nếu có
            $this->storeOrUpdateContract($employee, $credentials);

            return successResponse("Tạo nhân viên thành công", ['redirect' => '/employees']);
        });
    }

    private function storeOrUpdateContract(Employee $employee, array $credentials)
    {
        if (!empty($credentials['contract_type_id'])) {
            $data = [
                'contract_type_id' => $credentials['contract_type_id'],
                'code' => generateUniqueCode('contracts'),
                'file_url' => uploadPdf('file_url'), // input name = file_pdf
                'salary' => $credentials['salary'],
                'start_date' => $credentials['start_date'],
                'end_date' => $credentials['end_date'],
            ];

            $latestContract = $employee->latestContract;
            if ($latestContract) {
                deleteImage($latestContract->file_url);
                $latestContract->update($data);
            } else {
                $employee->contracts()->create($data);
            }
        }
    }

    public function update(EmployeeRequest $request, $id)
    {
        // dd($request->validated());
        $this->authorize('edit', Employee::class);

        $employee = Employee::with('user')->findOrFail($id);

        return transaction(function () use ($request, $employee) {
            $credentials = $request->validated();
            $credentials['code'] ??= $this->generateEmployeeCode();

            // Hash mật khẩu nếu có
            if (!empty($credentials['password'])) {
                $credentials['password'] = bcrypt($credentials['password']);
            } else {
                unset($credentials['password']);
            }

            // Cập nhật nhân viên
            $employee->update($credentials);

            // Cập nhật hoặc thêm mới hợp đồng
            $this->storeOrUpdateContract($employee, $credentials);

            $user = User::query()->where('employee_id', $employee->id)->firstOrFail();

            $user->update([
                'employee_id' => $employee->id,
                'name' => $employee->full_name,
                'email' => $employee->email,
                'avatar' => $employee->avatar,
                'phone' => $employee->phone,
            ] + ($request->filled('password') ? ['password' => bcrypt($request->input('password'))] : []));

            $user->syncPermissions($credentials['permissions']);

            return successResponse("Lưu thay đổi thành công", ['redirect' => '/employees']);
        });
    }

    private function generateEmployeeCode(): string
    {
        // Tìm mã lớn nhất hiện tại trong database
        $lastCode = Employee::query()
            ->where('code', 'like', 'NS%')
            ->orderByDesc(DB::raw('CAST(SUBSTRING(code, 3) AS UNSIGNED)'))
            ->value('code');

        if (!$lastCode) {
            return 'NS00001';
        }

        // Lấy phần số phía sau mã
        $number = (int) Str::after($lastCode, 'NS');
        $nextNumber = $number + 1;

        // Luôn pad đến 5 chữ số
        return 'NS' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function showPermissionForm()
    {
        $employees = Employee::all();
        $permissions = $this->groupPermissionsByNamespace();
        return view('backend.employee.permission', compact('permissions', 'employees'));
    }

    public function assignPermissions(Request $request)
    {
        $credentials = $request->validate(
            [
                'employee_id' => 'required|exists:employees,id',
                'permissions' => 'required|array',
                'permissions.*' => 'string|exists:permissions,name',
            ],
            __('request.messages'),
            [
                'employee_id' => 'Nhân viên',
                'permissions' => 'Quyền'
            ]
        );

        $user = User::query()->where('employee_id', $credentials['employee_id'])->firstOrFail();

        $user->syncPermissions($credentials['permissions']);

        return successResponse('Gán quyền cho nhân viên thành công.', ['redirect' => '/employees']);
    }

    private function groupPermissionsByNamespace()
    {
        return Permission::query()
            ->select('id', 'name', 'group_name', 'vi_name')
            ->orderBy('group_name', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('group_name');
    }
}
