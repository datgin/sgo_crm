@extends('backend.layouts.app')

@section('content')
    <x-breadcrumb />

    <x-page-header title="{{ isset($contractType) ? 'Cập nhật hợp đồng' : 'Thêm hợp đồng' }}">
        <a href="/contactTypes" class="btn btn-primary">
            <i class="fas fa-list me-1"></i> Danh sách hợp đồng
        </a>
    </x-page-header>
    <div class="card ">
        <div class="card-header">
            <h5 class="mb-0">Hợp đồng và xem file tài liệu</h5>
        </div>
        <div class="card-body">
            <form id="uploadForm" method="POST"
                action="{{ isset($contractType) ? route('contactTypes.update', $contractType->id) : route('contactTypes.store') }}"
                enctype="multipart/form-data">
                @csrf
                @if (isset($contractType))
                    @method('PUT')
                @endif
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên hợp đồng</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên hợp đồng"
                        value="{{ isset($contractType) ? $contractType->name : '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Chọn file (PDF)</label>
                    <div class="input-group">
                        <label for="fileInput" class="btn btn-outline-primary">
                            <i class="fas fa-file-upload"></i> Chọn tệp
                        </label>
                        <input type="file" name="file_url" id="fileInput" class="d-none" accept=".pdf">
                        <input type="text" id="fileName" class="form-control" placeholder="Chưa chọn file nào" readonly
                            value="{{ isset($contractType) && $contractType->file_url ? basename($contractType->file_url) : '' }}">
                    </div>
                </div>


                <button type="submit" class="btn btn-success">
                    <i class="fas fa-upload"></i> Tải lên
                </button>
            </form>

            <div id="previewArea" class="mt-4" style="{{ isset($contractType->file_url) ? '' : 'display: none;' }}">
                <h6>Xem trước tài liệu:</h6>
                <iframe src="{{ isset($contractType) ? asset($contractType->file_url) : '' }}" id="filePreview"
                    width="100%" height="600px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
@endsection
@push('styles')
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#fileInput').on('change', function(e) {
                const file = e.target.files[0];
                const preview = $('#filePreview');

                if (file) {

                    $('#fileName').val(file.name);

                    const fileType = file.type;
                    const fileName = file.name.toLowerCase();
                    const blobURL = URL.createObjectURL(file);

                    if (fileType === 'application/pdf' || fileName.endsWith('.pdf')) {
                        preview.attr('src', blobURL);
                        $('#previewArea').show();
                    } else if (fileName.endsWith('.docx')) {
                        alert(
                            "DOCX không thể xem trực tiếp — cần upload lên server để dùng Google Docs Viewer."
                        );
                        $('#previewArea').hide();
                    } else {
                        alert('Chỉ hỗ trợ file PDF hoặc DOCX.');
                        $('#previewArea').hide();
                    }
                }
            });

            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this)[0];
                const formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    method: form.method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: response.message,
                        });

                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1000);
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON?.message || 'Đã có lỗi xảy ra.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage,
                        });
                    }
                });
            });
        });
    </script>
@endpush
