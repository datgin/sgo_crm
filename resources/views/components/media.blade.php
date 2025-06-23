@php
    $uid = uniqid('media_');
@endphp

<div id="{{ $uid }}_upload_wrapper" class="btn-open-media upload-glow-border" data-name="{{ $name }}"
    data-multiple="{{ $multiple ? 'true' : 'false' }}" data-selected='@json($selected)'
    data-uid="{{ $uid }}">
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

    <div id="{{ $uid }}_selected-images-input" data-name="{{ $name }}"></div>

</div>


@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const uid = "{{ $uid }}";
            const selected = @json($selected);
            const multiple = {{ $multiple ? 'true' : 'false' }};

            if (!selected || Object.keys(selected).length === 0) return;

            if (!window.selectedImages) window.selectedImages = {};
            if (!selectedImages[uid]) selectedImages[uid] = new Map();

            if (Array.isArray(selected)) {

                selected.forEach((path) => {
                    const uniqueId = Date.now().toString() + Math.floor(Math.random() * 1000000);
                    selectedImages[uid].set(uniqueId, path);
                });
            }

            window.mediaPopup.currentUid = uid;
            window.mediaPopup.multiple = multiple;

            window.mediaPopup.handleSelect();
        });
    </script>
@endpush
