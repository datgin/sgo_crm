@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 border shadow-sm bg-white rounded">
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-light me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0">Danh sách hợp đồng</h1>
        </div>
       
    </div>


    <div class="card-body">
        <div class="table-responsive">
            <table id="myTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã nhân viên</th>
                        <th>Tên nhân viên</th>
                        <th>Vị trí</th>
                        <th>Phòng ban</th>
                        <th>Hợp đông</th>
                        <th>File</th>
                        <th>Thời gian</th>
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
                serverSide: false,
                order: [],
                ajax: {
                    url: "/contracts",
                },
                columns: [{
                        data: 'stt',
                        width: '2%',
                        orderable: false,
                        searchable: false
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
                        data: 'contract_type'
                    },
                    {
                        data: 'file'
                    },
                    {
                        data: 'date'
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
        });
    </script>
    </script>
@endpush
