@extends('backend.layouts.app')

@section('content')
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
