@extends('backend.layouts.app')

@section('content')
    <div class="card-body">
        <div class="table-responsive">
            <table id="myTable" class="display" style="width:100%">
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
                    url: '{{ route('contactTypes.index') }}',
                    type: 'GET',
                },
                columns: [{
                        data: 'checkbox',
                        title: '<input type="checkbox" id="check-all">',
                        orderable: false,
                        searchable: false,
                        width: '3%',
                    }, {
                        data: 'stt',
                        title: 'STT',
                        orderable: false,
                        searchable: false,
                        width: '5%'
                    },
                    {
                        data: 'name',
                        title: 'Tên loại hợp đồng'
                    },
                    {
                        data: 'file_url',
                        title: 'File',
                        orderable: false,
                        searchable: false,
                        width: '25%',
                    },
                    {
                        data: 'operations',
                        title: 'Hoạt động',
                        orderable: false,
                        searchable: false
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

            $(document).on('change', '#check-all', function() {
                $('.row-checkbox').prop('checked', this.checked);
            });

            $(document).on('change', '.row-checkbox', function() {
                if (!this.checked) {
                    $('#check-all').prop('checked', false);
                } else if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
                    $('#check-all').prop('checked', true);
                }
            });

            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                window.location.href = `/contactTypes/${id}/edit`; // hoặc nối thêm nếu thiếu /edit
            });
        });
    </script>
@endpush
