@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <x-page-header title="Cấu hình hạng mục">

    </x-page-header>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" id="multiModelTable">
                <thead>
                    <tr>
                        <th>Vị Trí</th>
                        <th>Phòng ban</th>
                        {{-- <th>Giới tính</th>
                    <th>Hợp đồng</th>
                    <th>Tình trạng làm việc</th> --}}
                        <th>Trình độ học vấn</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td contenteditable="true" data-id="{{ $row['positions']?->id }}" data-model="Position"
                                data-field="name">
                                {{ $row['positions']?->name }}
                            </td>

                            <td contenteditable="true" data-id="{{ $row['departments']?->id }}" data-model="Department"
                                data-field="name">
                                {{ $row['departments']?->name }}
                            </td>

                            {{-- <td contenteditable="true"
                            data-id="{{ $row['gender']?->id }}"
                            data-model="gender"
                            data-field="value">
                            {{ $row['gender']?->value }}
                        </td>

                        <td contenteditable="true"
                            data-id="{{ $row['contractType']?->id }}"
                            data-model="contractType"
                            data-field="name">
                            {{ $row['contractType']?->name }}
                        </td>

                        <td contenteditable="true"
                            data-id="{{ $row['status']?->id }}"
                            data-model="status"
                            data-field="name">
                            {{ $row['status']?->name }}
                        </td> --}}

                            <td contenteditable="true" data-id="{{ $row['educations']?->id }}" data-model="EducationLevel"
                                data-field="name">
                                {{ $row['educations']?->name }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-outline-primary add-row-btn">
                <i class="fas fa-plus"></i> Thêm hàng mới
            </button>
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
    <script>
        function attachBlurEvent() {
            const $tds = $('#multiModelTable td[contenteditable=true]');


            $tds.off('focus').on('focus', function() {
                const td = $(this);
                td.data('lastValue', td.text().trim());
            });


            $tds.off('blur').on('blur', function() {
                const td = $(this);
                const value = td.text().trim();
                const lastValue = td.data('lastValue') ?? '';
                const model = td.data('model');
                const field = td.data('field');
                let id = td.data('id');


                if (value === lastValue) {
                    return;
                }

                if (!model || !field) return;

                $.ajax({
                    url: '/categorys/update-or-create',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        model: model,
                        id: id ?? null,
                        field: field,
                        value: value
                    },
                    success: function(res) {
                        if (res.deleted) {
                            td.text('');
                            td.removeAttr('data-id');
                            console.log('🗑️ ' + res.message);
                        } else {
                            if (res.id) td.data('id', res.id);
                            td.data('lastValue', value);
                            console.log('✅ ' + res.message);
                        }
                    },
                    error: function() {
                        alert(`❌ Lỗi khi xử lý ${model}`);
                    }
                });
            });
        }


        $(document).ready(function() {
            attachBlurEvent();

            $('.add-row-btn').on('click', function() {
                const newRow = `
            <tr>
                <td contenteditable="true" data-model="Position" data-field="name"></td>
                <td contenteditable="true" data-model="Department" data-field="name"></td>
                <td contenteditable="true" data-model="EducationLevel" data-field="level_name"></td>
            </tr>`;
                $('#multiModelTable tbody').append(newRow);
                attachBlurEvent();
            });
        });
    </script>
@endpush
