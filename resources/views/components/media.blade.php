@php
    $uid = uniqid('media_');
@endphp

<div id="{{ $uid }}_upload_wrapper" class="btn-open-media upload-glow-border" data-name="{{ $name }}"
    data-multiple="{{ $multiple }}" data-selected='@json($selected)' data-uid="{{ $uid }}">
    <div class="upload-wrapper rounded p-3 bg-light text-center d-flex justify-content-center align-items-center"
        style="cursor: pointer; min-height: 220px;">
        <div id="{{ $uid }}_upload-preview"
            class="upload-preview d-flex flex-wrap gap-3 justify-content-start align-items-start">
        </div>
        <div id="{{ $uid }}_placeholder_text" class="placeholder-text text-muted">
            <i class="fas fa-cloud-upload-alt fs-3 d-block mb-2"></i>
            Bấm để chọn ảnh
        </div>
    </div>
    <input type="hidden" name="{{ $name }}" class="selected-images-input" value='@json($selected)'>
</div>


@once
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const $wrapper = document.getElementById("{{ $uid }}_upload_wrapper");
                const uid = "{{ $uid }}";
                const multiple = {{ $multiple ? 'true' : 'false' }};
                const selected = @json($selected);

                if (!selected || (typeof selected === 'object' && Object.keys(selected).length === 0)) {
                    return;
                }

                if (!window.selectedImgs) window.selectedImgs = {};
                if (!selectedImgs[uid]) selectedImgs[uid] = new Map();

                // Gán ảnh đã chọn vào selectedImgs (ID giả từ 1000000 trở lên)
                if (typeof selected === 'string') {
                    // Trường hợp chỉ 1 ảnh (multiple = false)
                    selectedImgs[uid].set(1000000, selected); // ID giả
                } else if (typeof selected === 'object' && selected !== null) {
                    // Trường hợp nhiều ảnh (multiple = true)
                    Object.entries(selected).forEach(([id, path]) => {
                        selectedImgs[uid].set(parseInt(id), path);
                    });
                }


                // Cập nhật giao diện preview ban đầu
                window.mediaPopup.currentUid = uid;
                window.mediaPopup.handleSelect();
            });
        </script>
    @endpush
@endonce
