@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 border shadow-sm bg-white rounded">
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-light me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="h4 mb-0">Thông tin hợp đồng của nhân viên : {{ $employee->full_name . '-' . $employee->code }} </h1>
        </div>
        {{-- <div class="d-flex gap-2">
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
        </div> --}}
    </div>


    <div class="card-body">
        <div class="table-responsive">
            <table id="myTable" class="display table table-bordered display" style="width:100%">
                <thead>
                    <tr>

                        <th>Vị trí</th>
                        <th>Phòng ban</th>
                        <th>Ngày công</th>
                        <th>Hợp đồng</th>
                        <th>Thời gian</th>
                        <th>File</th>
                    </tr>
                </thead>
                <tbody id="workday-table-body">
                    @foreach ($contracts as $item)
                        <tr>

                            <td>{{ $item->employee->full_name }}</td>
                            <td>{{ $item->employee->position->name }}</td>
                            <td>{{ $item->employee->department->name }}</td>
                            <td>{{ $item->contractType->name }}</td>
                            <td>{{ $item->start_date->format('d/m/Y') . ' - ' . $item->end_date->format('d/m/Y') }}</td>
                            <td><a href="{{ asset($item->file_url) }}" target="_blank">Tải file</a></td>
                        </tr>
                    @endforeach

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
@endpush
