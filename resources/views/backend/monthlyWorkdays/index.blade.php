@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 border shadow-sm bg-white rounded">
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-light me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0">Bảng công tháng</h1>
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
            <table id="myTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã NV</th>
                        <th>Tên nhân viên</th>
                        <th>Vị trí</th>
                        <th>Phòng ban</th>
                        <th>Ngày công</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
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
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="{{ asset('assets/backend/js/datatables.min.js') }}"></script>
    <script src="{{ asset('global/js/dataTables.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#myTable').DataTable({
                processing: true,
                serverSide: false, // vì bạn load toàn bộ 1 lần
                ajax: {
                    url: "{{ route('monthlyWorkdays.index') }}",
                    data: function(d) {
                        d.month = $('#monthSelector').val();
                    }
                },
                columns: [{
                        data: 'stt',
                        width: '2%'
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'full_name'
                    },
                    {
                        data: 'position'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'workdays'
                    },
                    {
                        data: 'salary'
                    },
                ],
                language: {
                    processing: "Đang xử lý...",
                    lengthMenu: "Hiển thị _MENU_ dòng mỗi trang",
                    zeroRecords: "Không tìm thấy dữ liệu phù hợp",
                    info: "Hiển thị _START_ đến _END_ của _TOTAL_ dòng",
                    infoEmpty: "Hiển thị 0 đến 0 của 0 dòng",
                    infoFiltered: "(lọc từ _MAX_ dòng)",
                    search: "Tìm kiếm:",
                    paginate: {
                        first: "Đầu",
                        last: "Cuối",
                        next: "Tiếp",
                        previous: "Trước"
                    },
                    emptyTable: "Không có dữ liệu trong bảng",
                    loadingRecords: "Đang tải...",
                }
            });

            $('#monthSelector').on('change', function() {
                table.ajax.reload();
            });

            $(document).on('blur', '.workday-input', function() {
                const input = $(this);
                const id = input.data('id');
                const workdays = input.val();

                $.ajax({
                    url: '/monthly-workdays/' + id + '/update-workdays',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        workdays: workdays
                    },
                    success: function(res) {
                        if (res.success) {
                            table.ajax.reload();
                            toastr.success(res.message);
                        } else {
                            table.ajax.reload();
                            toastr.error(res.message || 'Có lỗi xảy ra!');
                        }
                    },
                    error: function(xhr) {
                        table.ajax.reload();
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let message = '';
                            for (let key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    message += errors[key].join(', ') + '\n';
                                }
                            }
                            toastr.error(message);
                        } else {
                            table.ajax.reload();
                            toastr.error('Cập nhật thất bại! Đã xảy ra lỗi hệ thống.');
                        }
                    }
                });
            });


        });
    </script>
    </script>
@endpush
