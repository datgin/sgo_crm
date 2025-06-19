@extends('backend.layouts.app')

@section('content')
    <div class="my-3">
        <div class="row align-items-center mb-3">
            <div class="col-md-4">
                <h4 class="mb-3"> 🔍 Bộ lọc theo tháng</h4>
                @php
                    $monthOptions = collect(range(1, 12))
                        ->mapWithKeys(function ($month) {
                            return [$month => 'Tháng ' . $month];
                        })
                        ->toArray();

                    $currentMonth = now()->month;
                @endphp

                <x-select id="monthFilter" name="month_filter" placeholder="tháng" :options="$monthOptions" :value="$currentMonth" />

            </div>

            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tổng số sinh nhật trong tháng:</th>
                                <th>Tổng số sinh nhật hôm nay:</th>
                                <th>Hôm nay ngày:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-warning">
                                <td class="fw-bold fs-5" id="total-month">{{ $total_in_month }}</td>
                                <td class="fw-bold fs-5" id="total-today">{{ $total_today }}</td>
                                <td class="fw-bold text-primary" id="today-date">{{ now()->format('d/m/Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
                        <th>Giới tính</th>
                        <th>Ngày sinh</th>
                        <th>Ngày SN tháng</th>
                        <th>Còn lại ngày</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dataTables.min.css') }}">
    <style>
        .dt-layout-row {
            margin-top: 15px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/backend/js/datatables.min.js') }}"></script>
    <script src="{{ asset('global/js/dataTables.js') }}"></script>
    <script>
        $(document).ready(function() {

            let table = $('#myTable').DataTable({
                processing: true,
                serverSide: false,
                order: [],
                ajax: {
                    url: '{{ route('birthdays.index') }}',
                    type: 'GET',
                    data: function(d) {
                        d.month = $('#monthFilter').val();
                    },
                    dataSrc: function(json) {
                        $('#total-month').text(json.total_in_month);
                        $('#total-today').text(json.total_today);
                        $('#today-date').text(json.today);

                        return json.data;
                    }
                },
                columns: [{
                        data: 'stt',
                        title: 'STT',
                        orderable: false,
                        searchable: false,
                        width: '2%'
                    },
                    {
                        data: 'code',
                        name: 'code',
                        width: '8%'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
                        width: '18%'
                    },
                    {
                        data: 'position',
                        name: 'position',
                        width: '11%'
                    },
                    {
                        data: 'department',
                        name: 'department',
                        width: '11%'
                    },
                    {
                        data: 'gender',
                        name: 'gender',
                        width: '8%'
                    },
                    {
                        data: 'birthday',
                        name: 'birthday',
                        width: '10%'
                    },
                    {
                        data: 'birthday_this_year',
                        name: 'birthday_this_year',
                        width: '10%'
                    },
                    {
                        data: 'days_left',
                        name: 'days_left',
                        width: '6%'
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


            $('#monthFilter').on('change', function() {
                $('#total-month').text()
                table.ajax.reload();
            });
        });
    </script>
@endpush
