@extends('backend.layouts.app')

@section('content')
    <x-table fileName="employee" />
@endsection

@push('scripts')
    <script>
        $(function() {
            const api = "/employees"
            dataTables(api)
        })
    </script>
@endpush
