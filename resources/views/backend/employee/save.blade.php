@extends('backend.layouts.app')


@section('content')
    @php
        $breadcrumbs = [
            ['label' => 'Nhân viên', 'url' => '/employees'],
            ['label' => $employee ? "Cập nhật nhân viên - $employee->full_name" : 'Tạo mới nhân viên'],
        ];
    @endphp
    <x-breadcrumb :breadcrumbs="$breadcrumbs" />

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
                                <x-input type="email" name="email" id="email" label="Email" required="true"
                                    value="{{ $employee->email ?? '' }}" />
                            </div>

                            <div class="col-md-6">
                                <x-input type="password" required="true" name="password" id="password"
                                    label="Mật khẩu {{ $employee ? 'mới (bỏ qua nếu không đổi)' : '' }}"
                                    placeholder="Mật khẩu" />
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

                            <div class="col-md-6">
                                <x-select label="Loại hợp động" name="contract_type_id" :value="$employee && $employee->latestContract
                                    ? $employee->latestContract->contract_type_id
                                    : ''"
                                    :options="$contractTypes" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="salary" id="salary" class="format-price" label="Mức lương"
                                    value="{{ formatPrice($employee->latestContract?->salary ?? '') }}" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="start_date" id="start_date" label="Ngày bắt đầu hợp đồng"
                                    :value="$employee &&
                                    $employee->latestContract &&
                                    $employee->latestContract->start_date
                                        ? $employee->latestContract->start_date->format('d-m-Y')
                                        : ''" />
                            </div>

                            <div class="col-md-6">
                                <x-date name="end_date" id="end_date" label="Ngày kết thúc hợp đồng" :value="$employee && $employee->latestContract && $employee->latestContract->end_date
                                    ? $employee->latestContract->end_date->format('d-m-Y')
                                    : ''" />
                            </div>

                            <div class="col-md-12">
                                <label for="fileName" class="form-label fw-medium">
                                    Tải hợp đồng file (PDF)
                                </label>

                                <div class="input-group">
                                    <label for="fileInput" class="btn btn-outline-primary">
                                        <i class="fas fa-file-upload"></i> Chọn tệp
                                    </label>
                                    <input type="file" name="file_url" id="fileInput" class="d-none" accept=".pdf">
                                    <input type="text" id="fileName" class="form-control"
                                        placeholder="{{ $employee && $employee->latestContract ? basename($employee->latestContract->file_url) : 'Chưa chọn file nào ' }}"
                                        readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" id="previewArea"
                    style="{{ $employee && $employee->latestContract ? '' : 'display: none;' }}">
                    <div class="card-header">
                        <h4 class="card-title fs-6 fw-medium">Xem trước tài liệu</h4>
                    </div>
                    <div class="card-body">
                        <iframe
                            src="{{ $employee && $employee->latestContract && $employee->latestContract->file_url ? fileExists($employee->latestContract->file_url) : '' }}"
                            id="filePreview" width="100%" height="600px" frameborder="0"></iframe>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Avatar + tình trạng + ghi chú --}}
            <div class="col-md-3">

                <x-submit />

                <x-card title="Ảnh 3x4" class="text-center">
                    {{-- <x-file name="avatar" :value="$employee->avatar ?? showImage('')" /> --}}
                    <x-media name="avatar" :selected="$employee->avatar ?? ''" />
                </x-card>

                <x-card title="Tình trạng làm việc">
                    <x-select placeholder="Tình trạng làm việc" :value="$employee->employment_status_id ?? ''" name="employment_status_id"
                        :options="$employeeStatuses" />
                </x-card>

                <x-card title="Ghi chú">
                    <x-textarea name="notes" :value="$employee->notes ?? ''" placeholder="Ghi chú" id="notes" />
                </x-card>

                <x-card title="Trạng thái">
                    <x-switch-checkbox :checked="$employee->status ?? true" />
                </x-card>

            </div>
        </div>
    </form>

    <x-media-popup />
@endsection

@push('scripts')
    <script>
        $(function() {
            // formValidator.set({
            //     code: "nullable|max:50",
            //     full_name: 'required|max:255',
            //     email: 'required|email|max:255',
            //     password: `{{ isset($employee) ? 'nullable' : 'required' }}|min:8|max:255|regex:^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[\\W_]).{8,}$`,
            //     phone: 'nullable|regex:^0\\d{9}$',
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
            //     avatar: "{{ isset($employee) ? 'nullable' : 'required' }}|file|mimes:jpeg,png,jpg,webp|max_size:2048"
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

            $(document).on('click', '.toggle-password', function() {
                const input = $($(this).attr('toggle'));
                console.log(input);

                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);

                // Thay đổi icon
                $(this).html(type === 'password' ? '<i class="far fa-eye"></i>' :
                    '<i class="far fa-eye-slash"></i>');
            });

            submitForm("#myForm", function(response) {
                window.location.href = response.data.redirect
            });

            $('#fileInput').on('change', function(e) {
                const file = e.target.files[0];
                const preview = $('#filePreview');

                if (file) {

                    $('#fileName').val(file.name);

                    const fileType = file.type;
                    const fileName = file.name.toLowerCase();
                    const blobURL = URL.createObjectURL(file);

                    if (fileType === 'application/pdf' || fileName.endsWith('.pdf')) {
                        preview.attr('src', blobURL);
                        $('#previewArea').show();
                    } else if (fileName.endsWith('.docx')) {
                        alert(
                            "DOCX không thể xem trực tiếp — cần upload lên server để dùng Google Docs Viewer."
                        );
                        $('#previewArea').hide();
                    } else {
                        alert('Chỉ hỗ trợ file PDF hoặc DOCX.');
                        $('#previewArea').hide();
                    }
                }
            });
        })
    </script>
@endpush
