@extends('backend.layouts.app')


@section('content')
    <x-breadcrumb />

    <x-page-header :title="$title" />

    <form action="" method="POST" enctype="multipart/form-data" id="myForm">
        @csrf

        @isset($employee)
            @method('PUT')
        @endisset

        <div class="row">
            {{-- Cột trái: Thông tin nhân viên --}}
            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-input name="code" id="code" label="Mã nhân viên"
                                    value="{{ $employee->code ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="full_name" id="full_name" label="Họ tên" required="true"
                                    value="{{ $employee->full_name ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="phone" id="phone" label="Số điện thoại"
                                    value="{{ $employee->phone ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="address" id="address" label="Địa chỉ"
                                    value="{{ $employee->address ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="birthday" id="birthday" label="Ngày sinh" :value="$employee && $employee->birthday ? $employee->birthday->format('d-m-Y') : ''" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Giới tính" name="gender" :value="$employee->gender ?? ''" :options="['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác']" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="cccd" id="cccd" label="Số CCCD"
                                    value="{{ $employee->cccd ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="cccd_issued_date" id="cccd_issued_date" label="Ngày cấp CCCD"
                                    :value="$employee && $employee->cccd_issued_date
                                        ? $employee->cccd_issued_date->format('d-m-Y')
                                        : ''" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="university_start_date" id="university_start_date" label="Ngày nhập học"
                                    :value="$employee && $employee->university_start_date
                                        ? $employee->university_start_date->format('d-m-Y')
                                        : ''" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="university_end_date" id="university_end_date" label="Ngày tốt nghiệp"
                                    :value="$employee && $employee->university_end_date
                                        ? $employee->university_end_date->format('d-m-Y')
                                        : ''" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Chức vụ" :value="$employee->position_id ?? ''" name="position_id" :options="$positions" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Phòng ban" :value="$employee->department_id ?? ''" name="department_id" :options="$departments" />
                            </div>

                            <div class="col-md-6">
                                <x-select label="Trình độ học vấn" :value="$employee->education_level_id ?? ''" name="education_level_id"
                                    :options="$educationLevels" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="resignation_date" id="resignation_date" label="Ngày nghỉ việc (nếu có)"
                                    placeholder="ngày nghỉ việc" :value="$employee && $employee->resignation_date
                                        ? $employee->resignation_date->format('d-m-Y')
                                        : ''" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Avatar + tình trạng + ghi chú --}}
            <div class="col-md-3">

                <x-submit />

                <x-card title="Ảnh 3x4" class="text-center">
                    <x-file name="avatar" :value="$employee->avatar ?? ''" />
                </x-card>

                <x-card title="Tình trạng làm việc">
                    <x-select placeholder="Tình trạng làm việc" :value="$employee->employment_status_id ?? ''" name="employment_status_id"
                        :options="$employeeStatuses" />
                </x-card>

                <x-card title="Ghi chú">
                    <x-input name="notes" :value="$employee->notes ?? ''" placeholder="Ghi chú" id="notes" type="textarea" />
                </x-card>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            // formValidator.set({
            //     code: "nullable|max:50",
            //     full_name: 'required|max:255',
            //     phone: 'nullbale|regex:/^0\d{9}$/',
            //     address: 'nullable|max:255',
            //     birthday: 'nullable|date_format:d-m-Y|before_today',
            //     gender: 'required|in:male,female,other',
            //     cccd: 'nullable|numeric|digits_between:9,12',
            //     cccd_issued_date: 'nullable|date_format:d-m-Y|before_or_equal:today',
            //     university_start_date: 'nullable|date_format:d-m-Y',
            //     university_end_date: 'nullable|date_format:d-m-Y|after_or_equal:university_start_date',
            //     position_id: 'required',
            //     department_id: 'required',
            //     education_level_id: 'required',
            //     employment_status_id: 'required',
            //     resignation_date: 'nullable|date_format:d-m-Y|after_or_equal:birthday',
            //     notes: 'nullable|max:1000',
            //     avatar: "required|file|mimes:jpeg,png,jpg,webp|max_size:2048"
            // }, {
            //     code: "Mã nhân viên",
            //     full_name: "Họ tên",
            //     phone: "Số điện thoại",
            //     address: "Địa chỉ",
            //     birthday: "Ngày sinh",
            //     gender: "Giới tính",
            //     cccd: "Số CCCD",
            //     cccd_issued_date: "Ngày cấp CCCD",
            //     university_start_date: "Ngày bắt đầu đại học",
            //     university_end_date: "Ngày kết thúc đại học",
            //     position_id: "Vị trí",
            //     department_id: "Phòng ban",
            //     education_level_id: "Trình độ học vấn",
            //     employment_status_id: "Tình trạng làm việc",
            //     resignation_date: "Ngày nghỉ việc",
            //     notes: "Ghi chú",
            //     avatar: "Ảnh đại diện"
            // });

            submitForm("#myForm", function(response) {
                window.location.href = response.data.redirect
            });
        })
    </script>
@endpush
