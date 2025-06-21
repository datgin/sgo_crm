<!-- Popup chọn ảnh -->
<div id="media_popup" class="image-popup shadow-lg rounded bg-white border"
    style="display: none; position: fixed; width: 90%; height: 90vh; z-index: 1000; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div class="popup-header border-bottom p-3 d-flex justify-content-between align-items-center bg-light cursor-move">
        <h5 class="mb-0">📁 Thư viện ảnh</h5>
        <button onclick="window.mediaPopup.close()" type="button" class="btn btn-sm btn-outline-danger" data-close><i
                class="fas fa-times-circle"></i></button>
    </div>

    <div class="popup-body d-flex" style="height: calc(90vh - 120px);">
        <div class="flex-grow-1 border-end overflow-auto p-3" style="background: #f9f9f9;">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex gap-2 align-items-center">
                    <input type="file" id="popup_upload_input" class="d-none" accept="image/*" multiple>
                    <button onclick="window.mediaPopup.upload()" type="button" class="btn btn-outline-primary btn-sm"
                        id="popup_upload_btn">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Tải ảnh lên
                    </button>
                    <button type="button" onclick="window.mediaPopup.delete()"
                        class="btn btn-outline-danger btn-sm d-none" id="delete_btn">
                        <i class="fas fa-trash-alt me-1"></i> Xoá ảnh đã chọn
                    </button>
                </div>
                <div class="input-group" style="max-width: 250px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm ảnh..."
                        id="popup_search_input">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>

            <div data-list></div>
        </div>
        <div class="p-3 overflow-auto h-100" style="flex: 0 0 18%; max-width: 18%; background: #fcfcfc;" data-detail>
            <div class="text-muted fst-italic">Chọn ảnh để xem thông tin</div>
        </div>
    </div>

    <div class="popup-footer p-3 border-top bg-light text-end">
        <button type="button" onclick="window.mediaPopup.handleSelect()" class="btn btn-primary" data-select><i
                class="bi bi-check2-circle"></i> Chọn ảnh</button>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('global/js/media.js') }}"></script>
@endpush

{{-- @push('scripts')
    <script>
        let currentTargetWrapper = null;
        let currentSelected = [];
        let allowMultiple = true;
        let allImages = [];

        $(function() {
            const $popup = $('#media_popup');
            const $list = $popup.find('[data-list]');
            const $detail = $popup.find('[data-detail]');
            const $selectBtn = $popup.find('[data-select]');
            const $closeBtn = $popup.find('[data-close]');
            const $uploadBtn = $('#popup_upload_btn');
            const $uploadInput = $('#popup_upload_input');
            const $searchInput = $('#popup_search_input');

            $(document).on('click', '.upload-wrapper', function() {
                currentTargetWrapper = $(this).closest('.media-upload-wrapper');
                allowMultiple = currentTargetWrapper.data('multiple') === true || currentTargetWrapper.data(
                    'multiple') === 'true';
                currentSelected = currentTargetWrapper.data('selected') || [];

                $popup.removeClass('d-none').addClass('show').show();
                loadImages();
            });

            $closeBtn.on('click', () => $popup.removeClass('show').hide());

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.image-popup, .upload-wrapper').length) {
                    $popup.removeClass('show').hide();
                }
            });

            $uploadBtn.on('click', () => $uploadInput.trigger('click'));

            $uploadInput.on('change', function(e) {
                const files = e.target.files;
                if (!files.length) return;
                const formData = new FormData();
                $.each(files, (i, file) => formData.append('file[]', file));

                $.ajax({
                    url: '/media/upload',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        loadImages();
                        $uploadInput.val('');
                    },
                    error: () => alert('Không thể upload ảnh')
                });
            });

            function loadImages(page = 1, keyword = '') {
                $.get(`/media?page=${page}&search=${keyword}`, function(res) {
                    allImages = res.data || [];
                    let html = '<div class="row g-3">';
                    $.each(allImages, function(i, img) {
                        const isActive = currentSelected.some(sel => sel.id === img.id);
                        html += `
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="ratio ratio-1x1 position-relative img-select border rounded ${isActive ? 'active' : ''}"
                                    data-id="${img.id}" data-path="${img.path}" style="cursor: pointer;">
                                    <img src="${img.path}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0">
                                    <i class="fas fa-check-circle selected-icon text-primary position-absolute top-0 end-0 me-1 mt-1 d-none"></i>
                                </div>
                            </div>`;
                    });
                    html += '</div>';
                    $list.html(html);
                });
            }

            $list.on('click', '.img-select', function() {
                const id = parseInt($(this).data('id'));
                const image = allImages.find(img => img.id === id);
                if (!image) return;

                if (allowMultiple) {
                    const index = currentSelected.findIndex(i => i.id === id);
                    if (index >= 0) {
                        currentSelected.splice(index, 1);
                        $(this).removeClass('active');
                    } else {
                        currentSelected.push({
                            id,
                            path: image.path
                        });
                        $(this).addClass('active');
                    }
                } else {
                    currentSelected = [{
                        id,
                        path: image.path
                    }];
                    $list.find('.img-select').removeClass('active');
                    $(this).addClass('active');
                }

                renderImageDetail(image);
            });

            function renderImageDetail(image) {
                $detail.html(`
                    <div class="border rounded p-2 mb-2" style="aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center;">
                        <img src="${image.path}" class="mw-100 mh-100 object-fit-contain" />
                    </div>
                    <div class="small text-break">
                        <p><strong>Tên:</strong> ${image.name || '(Không có)'}</p>
                        <p><strong>URL:</strong><code>${image.path}</code></p>
                        <p><strong>Size:</strong> ${image.size || '...'} KB</p>
                        <p><strong>Ngày tải lên:</strong> ${image.uploaded_at || '...'}</p>
                        <p><strong>Chiều rộng:</strong> ${image.width || '...'} px</p>
                        <p><strong>Chiều cao:</strong> ${image.height || '...'} px</p>
                    </div>
                `);
            }

            $selectBtn.on('click', function() {
                if (!currentTargetWrapper) return;
                const $preview = currentTargetWrapper.find('.upload-preview');
                const $input = currentTargetWrapper.find('.selected-images-input');
                const $placeholder = currentTargetWrapper.find('.placeholder-text');

                let html = '';
                $.each(currentSelected, function(i, img) {
                    console.log(i, img);

                    html += `
                        <div class="position-relative selected-img" data-id="${img.id}" style="width: 100px; height: 100px; flex-shrink: 0;">
                            <div class="w-100 h-100 position-relative overflow-hidden rounded">
                                <img src="${img.path}" class="img-thumbnail w-100 h-100 object-fit-cover rounded">

                                <!-- Overlay đen mờ -->
                                <div class="overlay-hover-image position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 gap-2 justify-content-center align-items-center" style="display: none;">
                                    <a href="${img.path}" data-lightbox="" class="btn-preview-img">
                                        <i class="fas fa-eye shadow" title="Xem ảnh"></i>
                                    </a>
                                    <i class="far fa-trash-alt btn-remove-img shadow" title="Xoá ảnh"></i>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $preview.html(html);
                $input.val(JSON.stringify(currentSelected));
                $placeholder.toggle(currentSelected.length === 0);
                $popup.removeClass('show').hide();
            });

            $searchInput.on('input', function() {
                loadImages(1, $(this).val());
            });
        })
    </script>
@endpush --}}
