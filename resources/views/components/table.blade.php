<div class="card-body">
    <div class="table-responsive">
        <table id="myTable" class="display" style="width:100%">
        </table>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="{{ asset("assets/backend/js/columns/{$fileName}.js") }}"></script>
    <script src="{{ asset('assets/backend/js/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/fixedColumns.min.js') }}"></script>
    <script src="{{ asset('global/js/dataTables.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/fixedColumns.dataTables.min.css') }}">

@endpush
