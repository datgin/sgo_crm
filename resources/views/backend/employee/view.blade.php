@extends('backend.layouts.app')

@section('content')
    {{-- <div class=""> --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="employee-detail-form">

                <div class="text-center border-bottom pb-3 mb-4">
                    <h3 class="fw-bold mb-2">THÔNG TIN CHI TIẾT NHÂN SỰ</h3>
                    <h5 class="mb-1 text-muted text-start">{{ $setting->company_name }}</h5>
                    @if ($setting->address)
                        <h4 class="mb-0 small text-muted text-start">Địa chỉ: {{ $setting->address }}</h4>
                    @endif
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="row align-items-center">
                            <div class="col">
                                <x-select name="employee_id" id="employeeSelect" placeholder="nhân viên"
                                    :options="$employees" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="photo-frame mb-3">
                                <img id="employeeAvatar" src="{{ $employee->avatar }}" alt="Ảnh nhân viên"
                                    class="img-fluid">
                            </div>
                            <h5 class="fw-bold mb-1" id="employeeFullName">{{ $employee->full_name }}</h5>
                            <p class="text-muted  mb-1">Giới tính: <span
                                    id="employeeGender">{{ $employee->gender_text }}</span></p>
                            <p class=" mb-1">Tuổi: <span id="employeeAge">{{  $employee->birthday ? $employee->birthday->age : 'chưa cập nhật' }} tuổi</span></p>
                            <p class=" mb-1">Bộ phận: <span
                                    id="employeeDepartment">{{ $employee->department->name }}</span></p>
                            <p class=" mb-0">Chức vụ: <span id="employeePosition">{{ $employee->position->name }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="info-section mb-4">
                            <h6 class="section-header">Thông tin cá nhân</h6>
                            <table class="table table-sm table-borderless info-table">
                                <tbody>
                                    <tr>
                                        <td class="label-col">Mã NV:</td>
                                        <td id="employeeCode">{{ $employee->code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Ngày sinh:</td>
                                        <td id="employeeBirthday">
                                            {{ $employee->birthday ? $employee->birthday->format('d/m/Y') : 'chưa cập nhật' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">SĐT:</td>
                                        <td id="employeePhone">{{ $employee->phone }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Số CCCD:</td>
                                        <td id="employeeCCCD">{{ $employee->cccd }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Địa chỉ hiện tại:</td>
                                        <td id="employeeAddress">{{ $employee->address }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="info-section mb-4">
                            <h6 class="section-header">Thông tin công việc</h6>
                            <table class="table table-sm table-borderless info-table">
                                <tbody>
                                    <tr>
                                        <td class="label-col">Thời gian vào làm:</td>
                                        <td id="employeeStartDate">
                                            {!! $employee->start_date !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Thời gian kết thúc hợp đồng:</td>
                                        <td id="employeeEndDate" class="text-danger">
                                            {!! $employee->end_date !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Thâm niên:</td>
                                        <td id="employeeSeniority">{{ $employee->seniority_detail }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Loại HĐ:</td>
                                        <td id="employeeContractType" class="text-warning">
                                            {!! $employee->contract_type !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Trạng thái:</td>
                                        <td id="employeeStatus"
                                            class="{{ $employee->status != '1' ? 'text-danger' : 'text-success' }}">
                                            {{ $employee->status == 1 ? 'Đang làm việc' : 'Đã nghỉ' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Ngày nghỉ việc:</td>
                                        <td id="employeeResignationDate">
                                            {!! $employee->resignation_date
                                                ? $employee->resignation_date->format('d/m/Y')
                                                : "<span class='text-muted'>Chưa xác định</span>" !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Ghi chú:</td>
                                        <td id="employeeNotes">{{ $employee->notes }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="info-section">
                            <h6 class="section-header">Học vấn</h6>
                            <table class="table table-sm table-borderless info-table">
                                <tbody>
                                    <tr>
                                        <td class="label-col">Trình độ học vấn:</td>
                                        <td id="employeeEducation">{{ $employee->educationLevel->name }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="info-section">
                            <h6 class="section-header">Liên hệ khẩn cấp</h6>
                            <table class="table table-sm table-borderless info-table">
                                <tbody>
                                    <tr>
                                        <td class="label-col">Hợp đồng lao động:</td>
                                        <td><a href="#" class="text-primary text-decoration-underline">Hỗ trợ làm hợp
                                                đồng</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- </div> --}}
@endsection
@push('styles')
    <style>
        .employee-detail-form {
            background: white;
            border: 2px solid rgb(139, 137, 137);
            padding: 30px;
            margin: 20px 0;
            font-size: 13px;
        }

        .photo-frame {
            position: relative;
            width: 100%;
            padding-top: 133.33%;
        }

        .photo-frame img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .section-header {
            background: #4a90e2;
            color: white;
            padding: 8px 15px;
            margin: 0 0 15px 0;
            font-weight: bold;
            font-size: 13px;
        }

        .info-table {
            font-size: 12px;
        }

        .info-table td {
            padding: 6px 12px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }

        .label-col {
            background: #f0f4f8;
            width: 280px;
            font-weight: normal;
            color: #555;
        }

        .info-table tbody tr:last-child td {
            border-bottom: none;
        }

        @media print {
            .employee-detail-form {
                border: 1px solid #000;
                margin: 0;
                padding: 20px;
            }

            .container-fluid {
                padding: 0;
            }
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {

            const urlTemplate = '{{ route('employees.view', ['id' => ':id']) }}';
            // Khi đổi select
            $('select[name="employee_id"]').on('change', function() {
                let id = $(this).val();
                if (!id) return;
                const url = urlTemplate.replace(':id', id);
                $.ajax({
                    url: url,
                    dataType: 'json',
                    success: function(d) {
                        console.log(d);
                        // Cập nhật ảnh & văn bản

                        $('#employeeAvatar').attr('src', d.avatar);
                        console.log($('#employeeAvatar').attr('src'));
                        $('#employeeFullName').text(d.full_name);
                        $('#employeeGender').text(d.gender_text);
                        $('#employeeAge').text(d.age);

                        // Personal info
                        $('#employeeBirthday').html(d.birthday);
                        $('#employeePhone').text(d.phone);
                        $('#employeeCCCD').text(d.cccd);
                        $('#employeeCode').text(d.code);
                        $('#employeeAddress').text(d.address);

                        // Work info
                        $('#employeeStartDate').html(d.start_date);
                        $('#employeeEndDate').html(d.end_date);
                        $('#employeeSeniority').text(d.seniority_detail);
                        $('#employeeContractType').html(d.contract_type);

                        // Trạng thái: đổi màu luôn
                        $('#employeeStatus')
                            .text(d.employment_status);
                        // .toggleClass('text-danger', d.employment_status === 'Nghỉ việc')
                        // .toggleClass('text-success', d.employment_status !== 'Nghỉ việc');

                        $('#employeeResignationDate').html(d.resignation_date);
                        $('#employeeNotes').text(d.notes);

                        // Education
                        $('#employeeEducation').text(d.education_level);
                        window.history.pushState(null, '', url);
                    },
                    error: function() {
                        alert('Không thể tải thông tin nhân viên.');
                    }
                });
            });
        });
    </script>
@endpush
