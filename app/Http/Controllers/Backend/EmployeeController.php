<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\Employee\EmployeeCollection;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                    ->editColumn('status', fn($row) => view('components.switch-checkbox', ['checked' => $row->status, 'id' => $row->id])->render())
                    ->editColumn('operations', fn($row) => view('components.operation', ['row' => $row])->render()),
                ['days_left_for_university', 'contract_link', 'status']

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
                'contract.contractType',
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

        $title          = "Tạo mới nhân viên";
        $employee       = null;

        $positions = Position::query()->pluck('name', 'id')->toArray();
        $departments = Department::query()->pluck('name', 'id')->toArray();
        $educationLevels = EducationLevel::query()->pluck('name', 'id')->toArray();
        $employeeStatuses = EmploymentStatus::query()->pluck('name', 'id')->toArray();
        $contractTypes = ContractType::query()->pluck('name', 'id')->toArray();

        if (!empty($id)) {
            $employee   = Employee::query()->findOrFail($id);
            $title      = "Chỉnh sửa nhân viên - {$employee->full_name}";
        }

        return view('backend.employee.save', compact('title', 'employee', 'positions', 'departments', 'educationLevels', 'employeeStatuses', 'contractTypes'));
    }

    public function store(EmployeeRequest $request)
    {
        $uploadAvatar = null;
        return transaction(function () use ($request, &$uploadAvatar) {
            $credentials = $request->validated();

            $credentials['code'] ??= $this->generateEmployeeCode();

            $credentials['password'] = bcrypt($credentials['password']);

            if ($request->hasFile('avatar')) {
                $uploadAvatar = uploadImages('avatar', 'employee');
                $credentials['avatar'] = $uploadAvatar;
            }

            $employee = Employee::query()->create($credentials);

            User::create([
                'name' => $employee->full_name,
                'email' => $employee->email,
                'avatar' => $uploadAvatar,
                'phone' => $employee->phone,
                'password' => $credentials['password'],
            ]);

            return successResponse("Tạo nhân viên thành công", ['redirect' => '/employees']);
        }, function () use ($uploadAvatar) {
            deleteImage($uploadAvatar);
        });
    }

    public function update(EmployeeRequest $request, $id)
    {
        $uploadAvatar = null;
        $employee = Employee::query()->findOrFail($id);
        $oldAvatar = $employee->getRawOriginal('avatar');
        $email = $employee->email;

        return transaction(function () use ($request, &$uploadAvatar, $oldAvatar, $employee, $email) {
            $credentials = $request->validated();

            $credentials['code'] ??= $this->generateEmployeeCode();

            // Hash password nếu có
            if (!empty($credentials['password'])) {
                $credentials['password'] = bcrypt($credentials['password']);
            } else {
                unset($credentials['password']); // Không cập nhật nếu không có
            }

            // Upload avatar nếu có
            if ($request->hasFile('avatar')) {
                $uploadAvatar = uploadImages('avatar', 'employee');
                $credentials['avatar'] = $uploadAvatar;
            }

            // Cập nhật nhân viên
            $employee->update($credentials);

            // Cập nhật user liên kết (nếu có), không tạo mới
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'name' => $employee->full_name,
                    'email' => $employee->email,
                    'avatar' => $uploadAvatar ?? $oldAvatar,
                    'phone' => $employee->phone,
                ] + ($request->filled('password') ? ['password' => bcrypt($request->input('password'))] : []));
            }

            if (!empty($uploadAvatar)) {
                deleteImage($oldAvatar);
            }

            return successResponse("Lưu thay đổi thành công", ['redirect' => '/employees']);
        }, function () use ($uploadAvatar) {
            deleteImage($uploadAvatar);
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
            return 'NS001';
        }

        // Lấy phần số phía sau mã
        $number = (int) Str::after($lastCode, 'NS');
        $nextNumber = $number + 1;

        // Tạo mã mới có dạng NS001 hoặc NS1000
        return 'NS' . str_pad($nextNumber, strlen($number < 1000 ? 3 : strlen((string)$nextNumber)), '0', STR_PAD_LEFT);
    }
}
