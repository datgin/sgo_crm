@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <x-page-header title="Cấu hình hạng mục">
        <button class="btn btn-outline-primary" id="add-row">
            <i class="fas fa-plus"></i> Thêm hàng mới
        </button>
    </x-page-header>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle" id="multiModelTable">
                <thead>
                    <tr>
                        <th>Vị Trí</th>
                        <th>Phòng ban</th>
                        <th>Loại hợp đồng</th>
                        <th>Tình trạng làm việc</th>
                        <th>Trình độ học vấn</th>
                    </tr>
                </thead>
                {{-- <tbody>
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


                            <td contenteditable="true" data-id="{{ $row['educations']?->id }}" data-model="EducationLevel"
                                data-field="name">
                                {{ $row['educations']?->name }}
                            </td>
                        </tr>
                    @endforeach
                </tbody> --}}

                <tbody id="excel-body">
                    @for ($i = 0; $i < $maxRows; $i++)
                        <tr>
                            <td>
                                <input type="text" class="form-control" data-table="positions"
                                    value="{{ $positions[$i] ?? '' }}" data-original="{{ $positions[$i] ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" data-table="departments"
                                    value="{{ $departments[$i] ?? '' }}" data-original="{{ $departments[$i] ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" data-table="contract_types"
                                    value="{{ $contract_types[$i] ?? '' }}" data-original="{{ $contract_types[$i] ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" data-table="employment_statuses"
                                    value="{{ $employment_statuses[$i] ?? '' }}"
                                    data-original="{{ $employment_statuses[$i] ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" data-table="education_levels"
                                    value="{{ $education_levels[$i] ?? '' }}"
                                    data-original="{{ $education_levels[$i] ?? '' }}">
                            </td>
                        </tr>
                    @endfor
                </tbody>

            </table>
        </div>
    </div>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dataTables.min.css') }}">
@endpush
@push('scripts')
    <script>
        // function attachBlurEvent() {
        //     const $tds = $('#multiModelTable td[contenteditable=true]');


        //     $tds.off('focus').on('focus', function() {
        //         const td = $(this);
        //         td.data('lastValue', td.text().trim());
        //     });


        //     $tds.off('blur').on('blur', function() {
        //         const td = $(this);
        //         const value = td.text().trim();
        //         const lastValue = td.data('lastValue') ?? '';
        //         const model = td.data('model');
        //         const field = td.data('field');
        //         let id = td.data('id');

        //         if (value === lastValue) {
        //             return;
        //         }

        //         if (!model || !field) return;

        //         $.ajax({
        //             url: '/categorys/update-or-create',
        //             type: 'POST',
        //             data: {
        //                 _token: '{{ csrf_token() }}',
        //                 model: model,
        //                 id: id ?? null,
        //                 field: field,
        //                 value: value
        //             },
        //             success: function(res) {
        //                 if (res.deleted) {
        //                     td.text('');
        //                     td.removeAttr('data-id');
        //                     console.log('🗑️ ' + res.message);
        //                 } else {
        //                     if (res.id) td.data('id', res.id);
        //                     td.data('lastValue', value);
        //                     console.log('✅ ' + res.message);
        //                 }
        //             },
        //             error: function() {
        //                 alert(`❌ Lỗi khi xử lý ${model}`);
        //             }
        //         });
        //     });
        // }


        $(document).ready(function() {
            $('#add-row').click(function() {
                const row = $('#excel-body tr:first').clone();
                row.find('input').val('').removeAttr('data-original');
                $('#excel-body').append(row);
            });

            $(document).on('blur', '#excel-body input', function() {
                const $input = $(this);
                const value = $input.val().trim();
                const table = $input.data('table');
                const originalValue = $input.data('original') || '';

                // 👉 Nếu không thay đổi gì thì không làm gì cả
                if (value === originalValue) return;

                // 👉 Nếu xóa nội dung
                if (value === '') {
                    if (!originalValue) return;

                    if (!confirm(`Bạn có chắc muốn xóa "${originalValue}"?`)) {
                        $input.val(originalValue);
                        return;
                    }

                    $.ajax({
                        url: '/categorys/excel-delete',
                        method: 'DELETE',
                        data: {
                            table: table,
                            name: originalValue,
                        },
                        success: function(response) {
                            $input.val('');
                            $input.removeAttr('data-original');
                            datgin.success(response.message)
                        },
                        error: function(xhr) {
                            datgin.error(xhr.responseJSON.message)
                            $input.val(originalValue);
                        }
                    });
                } else {
                    $.ajax({
                        url: '/categorys/excel-save',
                        method: 'POST',
                        data: {
                            table: table,
                            name: value,
                            original_name: originalValue // nếu muốn xử lý cập nhật
                        },
                        success: function(response) {
                            $input.data('original', value); // cập nhật lại gốc
                            datgin.success(response.message)
                        },
                        error: function(xhr) {
                            $input.val('')
                            datgin.error(xhr.responseJSON.message)
                        }
                    });
                }
            });
        });
    </script>
@endpush
