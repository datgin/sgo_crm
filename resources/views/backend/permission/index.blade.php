@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb :breadcrumbs="[['label' => 'Nhân viên']]" />

    <div class="row">
        <div class="col-lg-5">
            <form action="" method="post" id="myForm">
                <input type="hidden" name="id">
                <input type="hidden" name="_method">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-uppercase card-title fw-medium" id="title-change">Thêm mới quyền</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-input name="vi_name" label="Tên quyền" :required="true" />
                            </div>

                            <div class="col-md-6">
                                <x-input name="name" label="Quyền" :required="true" />
                            </div>

                            <div class="col-md-12">
                                <label for="group_name" class="form-label fw-medium">Nhóm quyền</label>
                                <input type="text" placeholder="Nhóm quyền" class="form-control" name="group_name"
                                    id="group_name" list="group_name_list">

                                <datalist id="group_name_list">
                                    @foreach ($groupNames as $groupName)
                                        <option value="{{ $groupName }}">{{ $groupName }}</option>
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary">Lưu thay đổi</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="resetForm()">Làm mới</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-uppercase card-title fw-medium">danh sách quyền</h5>
                </div>

                <x-table fileName="permission" />

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let oldId = null;

        function resetForm() {
            $('#myForm')[0].reset();
            $('#myForm').removeAttr('data-product-id');
            $('#title-change').text('Thêm mới quyền');
            $('input[name="id"]').val('');
            $('input[name="id"]').val('');
            oldId = null
        }

        $(document).ready(function() {

            dataTables('/permissions')

            handleDestroy('Permission')

            initBulkAction('Permission')

            submitForm("#myForm", function(response) {
                datgin.success(response.message)
                $("table").DataTable().ajax.reload();
                resetForm()
            });

            $(document).on('click', '.btn-edit', function(e) {
                e.preventDefault();

                let $button = $(this);

                let $id = $button.data('id');

                if (oldId == $id) return

                oldId = $id

                $('input[name="_method"]').val('put');

                $.get(`/permissions/${$id}`, function(response) {
                    const permission = response.data

                    $.each(permission, function(key, value) {
                        $(`input[name="${key}"]`).val(value)
                    });

                    $('#title-change').text(`Cập nhật quyền - ${permission.vi_name}`)

                }).fail(function(xhr) {
                    datgin.error(xhr.responseJSON.message)
                })

            });
        })
    </script>
@endpush
