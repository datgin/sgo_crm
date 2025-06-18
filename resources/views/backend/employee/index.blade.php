@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <x-page-header title="Danh sách nhân viên">
        <a href="/employees/save" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tạo mới nhân viên
        </a>
    </x-page-header>

    <x-table fileName="employee" />
@endsection

@push('scripts')
    <script>
        $(function() {
            const api = "/employees"
            dataTables(api, {
                fixedColumns: {
                    left: 6,
                    right: 1
                },
            })
        })
    </script>
@endpush
