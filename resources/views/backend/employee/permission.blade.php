@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb :breadcrumbs="[['label' => 'Nhân viên', 'url' => '/employees'], ['label' => 'Gán quyền hạn cho nhân viên']]" />

    <form id="myForm">

        <x-card>
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <select name="employee_id" id="employee_id" class="form-select">
                        <option value="">-- Chọn nhân viên --</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ old('employee_id', $selectedEmployeeId ?? '') == $employee->id ? 'selected' : '' }}>
                                {{ "$employee->full_name - $employee->code" }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Lưu quyền
                    </button>
                </div>
            </div>
        </x-card>

        <x-card title="Danh sách quyền hạn">
            @foreach ($permissions as $groupName => $permission)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                        <div>
                            <strong>{{ $groupName }}</strong>
                            <span class="badge bg-light text-dark ms-2">{{ count($permission) }} quyền</span>
                        </div>
                        <div>
                            <input type="checkbox" class="form-check-input select-all cursor"
                                id="selectAll-{{ \Str::slug($groupName) }}">
                            <label for="selectAll-{{ \Str::slug($groupName) }}"
                                class="form-check-label ms-1 text-white cursor">Chọn tất cả</label>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-3">
                        @foreach ($permission as $item)
                            <div class="form-check">
                                <input class="form-check-input cursor" type="checkbox" name="permissions[]"
                                    id="{{ \Str::slug($item->name) }}" value="{{ $item->name }}"
                                    @checked(in_array($item->name, !empty($assignedPermissions) ? $assignedPermissions : []))>
                                <label class="form-check-label mb-0 cursor"
                                    for="{{ \Str::slug($item->vi_name) }}">{{ $item->vi_name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </x-card>

    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            submitForm("#myForm", function(response) {
                window.location.href = response.data.redirect
            });
        })

        $('.card.mb-3').each(function() {
            const card = $(this);

            const allChecked = card.find('input[type="checkbox"]:not([id^="selectAll"])').length > 0 &&
                card.find('input[type="checkbox"]:not([id^="selectAll"])').length ===
                card.find('input[type="checkbox"]:not([id^="selectAll"]):checked').length;

            card.find('.select-all').prop('checked', allChecked);

            // Sự kiện khi nhấn "Chọn tất cả"
            card.find('.select-all').on('change', function() {
                const isChecked = $(this).is(':checked');
                card.find('input[type="checkbox"]:not([id^="selectAll"])').prop('checked',
                    isChecked);
            });

            // Sự kiện khi checkbox con thay đổi
            card.find('input[type="checkbox"]:not([id^="selectAll"])').on('change', function() {
                const allChecked = card.find('input[type="checkbox"]:not([id^="selectAll"])')
                    .length ===
                    card.find('input[type="checkbox"]:not([id^="selectAll"]):checked').length;
                card.find('.select-all').prop('checked', allChecked);
            });
        });
    </script>
@endpush
