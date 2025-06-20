@php
    $uid = uniqid('upload_');
@endphp

<div class="upload-wrapper">
    <label for="{{ $uid }}" class="upload-label d-block position-relative" style="cursor: pointer">
        <img src="{{ $value }}" alt="Preview" class="preview-image"
            style="width: {{ $width }}; height: {{ $height }}; object-fit: cover; border: 1px solid #ccc;">
        <input type="file" id="{{ $uid }}" name="{{ $multiple ? $name . '[]' : $name }}"
            {{ $multiple ? 'multiple' : '' }} class="d-none" accept="{{ $accept }}">
        <small class="text-danger error-message {{ $name }}"></small>
    </label>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('{{ $uid }}');
            const preview = input.closest('.upload-wrapper').querySelector('.preview-image');

            input.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files.length === 0) return;

                const file = files[0];
                if (!file.type.startsWith('image/')) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endpush
