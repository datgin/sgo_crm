{{-- resources/views/backend/employee/partials/employee_list.blade.php --}}
@forelse ($employees as $employee)
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="employee-card">
            <img src="{{ asset($employee->avatar) }}" alt="{{ $employee->full_name }}" class="employee-photo">
            <div class="employee-name">{{ $employee->full_name . '-' . $employee->code }}</div>
            <div class="employee-title">{{ $employee->position->name }}</div>
            <div class="employee-department">{{ $employee->department->name }}</div>

            @php
                $status = $employee->employmentStatus->name;
                $isWorking = $status == 'Đang làm việc';
            @endphp

            <span class="badge {{ $isWorking ? 'text-success' : 'text-danger' }}">
                <i class="fas fa-circle"></i> {{ $status }}
            </span>

            <div class="mt-2"><a href="{{ route('employees.view', ['id' => $employee->id]) }}" class="details-link">Xem thông tin chi tiết </a></div>
        </div>
    </div>
@empty
    <p class="text-center">Không có nhân viên nào.</p>
@endforelse
