<div class="employee-grid">
    @forelse ($employees as $employee)
        <div class="employee-card">
            <img src="{{ $employee->avatar }}" alt="{{ $employee->full_name }}" class="employee-photo">
            <div class="employee-name">{{ $employee->code . ' - ' . $employee->full_name }}</div>
            <div class="employee-title">{{ $employee->position->name }}</div>
            <div class="employee-department">{{ $employee->department->name }}</div>

            @php
                $status = $employee->status == 1 ? 'Đang làm việc' : 'Nghỉ việc';
            @endphp

            <span class="badge {{ $employee->status == 1 ? 'text-success' : 'text-danger' }}">
                <i class="fas fa-circle"></i> {{ $status }}
            </span>

            <div class="mt-2">
                <a href="{{ route('employees.view', ['id' => $employee->id]) }}" class="details-link">
                    Xem thông tin chi tiết
                </a>
            </div>
        </div>
    @empty
        <p class="text-center">Không có nhân viên nào.</p>
    @endforelse
</div>
