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

    <style>
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #adb5bd;
            border-radius: 10px;
            border: 1px solid transparent;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: #6c757d;
        }

        /* ---------- Scrollbar cho Firefox ---------- */
        * {
            scrollbar-width: thin;
            scrollbar-color: #adb5bd transparent;
        }
    </style>
@endpush
