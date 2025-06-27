@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 border shadow-sm bg-white rounded">
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-light me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0">Bảng lương tháng</h1>
        </div>
        <div class="d-flex gap-2">
            <select id="monthSelector" class="form-select w-auto">
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $value = now()->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <option value="{{ $value }}" {{ $value == $month ? 'selected' : '' }}>
                        Tháng {{ $i }}/{{ now()->year }}
                    </option>
                @endfor
            </select>
        </div>
    </div>


    <div class="card-body">
        <div class="table-responsive">
            <table id="myTable" class="display table table-bordered display" style="width:100%">
                <thead>
                    <tr>
                        <th>Mã nhân viên</th>
                        <th>Tên nhân viên</th>
                        <th>Vị trí</th>
                        <th>Phòng ban</th>
                        <th>Ngày công</th>
                        <th>Tổng tiền</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody id="workday-table-body">
                    @if ($monthlyWorkday)
                        <tr>
                            <td>{{ $monthlyWorkday->employee->code }}</td>
                            <td>{{ $monthlyWorkday->employee->full_name }}</td>
                            <td>{{ $monthlyWorkday->employee->position->name }}</td>
                            <td>{{ $monthlyWorkday->employee->department->name }}</td>
                            <td>{{ $monthlyWorkday->workdays }}</td>
                            <td>{{ formatPrice($monthlyWorkday->salary, true) }}</td>
                            <td><a href="{{ asset($monthlyWorkday->file) }}" target="_blank">Tải file</a></td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endif
                </tbody>

            </table>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dataTables.min.css') }}">
    <style>
        .dt-layout-row {
            margin-top: 15px !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#monthSelector').on('change', function() {
                const selectedMonth = $(this).val();
                $.ajax({
                    url: '/employee-monthly-workdays',
                    type: 'GET',
                    data: {
                        month: selectedMonth
                    },
                    success: function(response) {
                        $('#workday-table-body').html(response.html);
                    },
                    error: function(xhr, status, error) {
                        console.error('Lỗi khi load dữ liệu:', error);
                    }
                });
            });
        });
    </script>
@endpush
