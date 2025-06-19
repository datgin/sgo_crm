@php
    $uid = uniqid('media_picker_');
@endphp

<!-- Khung upload ảnh -->
<div id="{{ $uid }}_upload_wrapper"
    class="border border-secondary rounded p-4 bg-light text-center d-flex justify-content-center align-items-center"
    style="cursor: pointer; min-height: 220px;">
    <div class="upload-preview d-flex flex-wrap gap-3 justify-content-start align-items-start"></div>
    <div class="placeholder-text text-muted fs-6">
        <i class="fas fa-cloud-upload-alt fs-3 d-block"></i>
        Bấm để chọn ảnh
    </div>
</div>
<input type="hidden" name="{{ $name }}" id="{{ $uid }}_selected_images"
    value='@json($selected)'>

<!-- Popup chọn ảnh -->
<div id="{{ $uid }}_popup" class="image-popup shadow-lg rounded bg-white d-none border"
    style="position: fixed; width: 90%; height: 90vh; z-index: 9999; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div class="popup-header border-bottom p-3 d-flex justify-content-between align-items-center bg-light cursor-move">
        <h5 class="mb-0">📁 Thư viện ảnh</h5>
        <button type="button" class="btn btn-sm btn-outline-danger" data-close><i
                class="fas fa-times-circle"></i></button>
    </div>

    <div class="popup-body d-flex" style="height: calc(90vh - 120px);">
        <div class="flex-grow-1 border-end overflow-auto p-3" style="background: #f9f9f9;">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex gap-2 align-items-center">
                    <input type="file" id="{{ $uid }}_upload_input" class="d-none" accept="image/*"
                        multiple>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="{{ $uid }}_upload_btn">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Tải ảnh lên
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm d-none"
                        id="{{ $uid }}_delete_btn">
                        <i class="fas fa-trash-alt me-1"></i> Xoá ảnh đã chọn
                    </button>
                </div>
                <div class="input-group" style="max-width: 250px;">
                    <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm ảnh..."
                        id="{{ $uid }}_search_input">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                </div>
            </div>

            <div data-list></div>
        </div>
        <div class=" p-3" style="flex: 0 0 18%; max-width: 18%; background: #fcfcfc;" data-detail
            style="background: #fcfcfc;">
            <div class="text-muted fst-italic">Chọn ảnh để xem thông tin</div>
        </div>
    </div>

    <div class="popup-footer p-3 border-top bg-light text-end">
        <button type="button" class="btn btn-primary" data-select><i class="bi bi-check2-circle"></i> Chọn ảnh</button>
    </div>
</div>

@once
    <style>
        #{{ $uid }}_delete_btn.d-none {
            display: none !important;
        }

        .image-popup {
            opacity: 0;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease-in-out;
            pointer-events: none;
        }

        .image-popup.show {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
            pointer-events: auto;
        }

        .img-select.active {
            outline: 1px solid #0d6efd;
            outline-offset: 2px;
            border-radius: 5px;
        }

        .img-select .selected-icon {
            font-size: 1.2rem;
            z-index: 2;
        }

        .img-select.active .selected-icon {
            display: block !important;
        }

        .upload-preview img {
            transition: all 0.3s ease-in-out;
        }

        .upload-preview .selected-img:hover {
            transform: scale(1.03);
            transition: transform 0.2s ease;
        }


        .object-fit-cover {
            object-fit: cover;
        }

        .cursor-move {
            cursor: move;
        }

        .fa-check-circle::before {
            border-radius: 12px;
            background-color: white
        }

        .selected-img:hover .overlay-hover-image {
            display: flex !important;
        }

        .overlay-hover-image {
            transition: background 0.3s ease-in-out;
        }

        .overlay-hover-image i {
            opacity: 0.8;
            color: #ffffff
        }
    </style>
@endonce

@pushOnce('scripts')
    <script>
        let allImages = [];

        $(function() {
            const uid = @json($uid);
            const $wrapper = $(`#${uid}_upload_wrapper`);
            const $popup = $(`#${uid}_popup`); // ✅ Fixed selector
            const $list = $popup.find('[data-list]');
            const $detail = $popup.find('[data-detail]');
            const $closeBtn = $popup.find('[data-close]');
            const $selectBtn = $popup.find('[data-select]');
            const $input = $(`#${uid}_selected_images`);
            const allowMultiple = @json($multiple ?? true);
            let selectedImages = @json($selected ?? []);
            const $uploadBtn = $(`#${uid}_upload_btn`);
            const $uploadInput = $(`#${uid}_upload_input`);

            $uploadBtn.on('click', () => $uploadInput.trigger('click'));

            $wrapper.on('click', function(e) {
                if ($(e.target).closest('.selected-img, .btn-remove-img, .btn-preview-img').length) {
                    return;
                }

                $popup.removeClass('d-none').css('display', 'block').addClass('show');
                loadImages();
            });


            $closeBtn.on('click', function() {
                $popup.addClass('d-none');
            });

            // ✅ Close popup when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.image-popup, #' + uid + '_upload_wrapper').length) {
                    $popup.removeClass('show');
                    setTimeout(() => $popup.addClass('d-none'), 250);
                }
            });

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
                    success: res => {
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
                    $.each(allImages, function(i, image) {
                        const isActive = selectedImages.some(i => i.id === image.id) ? 'active' :
                            '';
                        html += `
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                <div class="ratio ratio-1x1 overflow-hidden position-relative img-select border rounded ${isActive}"
                                    data-id="${image.id}"
                                    data-path="${image.path}"
                                    style="cursor: pointer;">
                                    <img src="${image.path}"
                                        class="w-100 h-100 object-fit-cover position-absolute top-0 start-0"
                                        alt="">
                                    <i class="fas fa-check-circle selected-icon text-primary position-absolute top-0 end-0 me-1 mt-1 d-none"></i>
                                </div>
                            </div>
                        `;

                    });
                    html += '</div>';
                    $list.html(html);
                }).fail(function() {
                    $list.html('<div class="text-danger">Không thể tải ảnh</div>');
                });
            }

            $list.on('click', '.img-select', function() {
                const id = parseInt($(this).data('id'));
                const image = allImages.find(img => img.id === id);
                if (!image) return;

                if (allowMultiple) {
                    const isSelected = selectedImages.some(i => i.id === id);
                    if (isSelected) {
                        // Bỏ chọn
                        selectedImages = selectedImages.filter(i => i.id !== id);
                        $(this).removeClass('active');
                    } else {
                        // Thêm vào danh sách
                        selectedImages.push({
                            id,
                            path: image.path
                        });
                        $(this).addClass('active');
                    }
                } else {
                    // Chế độ 1 ảnh
                    $list.find('.img-select').removeClass('active');
                    selectedImages = [{
                        id,
                        path: image.path
                    }];
                    $(this).addClass('active');
                }

                // Cập nhật preview chi tiết (chỉ lấy cái cuối được chọn hoặc duy nhất)
                const lastImage = allImages.find(img => img.id === id);
                if (lastImage) {
                    $detail.html(`
                        <div class="border rounded p-2 mb-2" style="width: 100%; aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center; background: #fff;">
                            <img src="${lastImage.path}" class="mw-100 mh-100 object-fit-contain" alt="">
                        </div>
                        <div class="text-break small">
                            <p><strong>Tên:</strong> ${lastImage.name || '(Không có)'}</p>
                            <p><strong>URL:</strong><br><code>${lastImage.path}</code></p>
                            <p><strong>Kích thước:</strong> ${lastImage.size || '...'} KB</p>
                            <p><strong>Ngày tải lên:</strong> ${lastImage.uploaded_at || '...'}</p>
                            <p><strong>Chiều rộng:</strong> ${lastImage.width || '...'} px</p>
                            <p><strong>Chiều cao:</strong> ${lastImage.height || '...'} px</p>
                        </div>
                    `);
                }
            });

            $selectBtn.on('click', function() {
                renderSelected();
                $popup.addClass('d-none');
            });

            function renderSelected() {
                const $preview = $wrapper.find('.upload-preview');
                const $placeholder = $wrapper.find('.placeholder-text');
                let html = '';

                $.each(selectedImages, function(i, img) {
                    html += `
                        <div class="position-relative selected-img" data-id="${img.id}" style="width: 100px; height: 100px; flex-shrink: 0;">
                            <div class="w-100 h-100 position-relative overflow-hidden rounded">
                                <img src="${img.path}" class="img-thumbnail w-100 h-100 object-fit-cover rounded">

                                <!-- Overlay đen mờ -->
                                <div class="overlay-hover-image position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 gap-2 justify-content-center align-items-center" style="display: none;">
                                    <a href="${img.path}" data-lightbox="preview-${uid}" class="btn-preview-img">
                                        <i class="fas fa-eye shadow" title="Xem ảnh"></i>
                                    </a>
                                    <i class="far fa-trash-alt btn-remove-img shadow" title="Xoá ảnh"></i>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $preview.html(html);
                $placeholder.toggle(selectedImages.length === 0);
                $input.val(JSON.stringify(selectedImages));
                $('#{{ $uid }}_delete_btn').toggleClass('d-none', selectedImages.length ===
                    0); // 👈 Hiển thị nút xoá
            }

            $wrapper.on('click', '.btn-remove-img', function(e) {
                e.stopPropagation();
                const id = parseInt($(this).closest('.selected-img').data('id'));
                selectedImages = selectedImages.filter(img => img.id !== id);
                renderSelected();
            });

            // $wrapper.on('click', '.btn-preview-img', function(e) {
            //     e.stopPropagation(); // Ngăn mở popup

            //     const $img = $(this).closest('.selected-img').find('img');
            //     const src = $img.attr('src');

            //     if (!src) return;
            // });


            // ✅ Check if Sortable exists before using
            if (typeof Sortable !== 'undefined') {
                new Sortable($wrapper.find('.upload-preview')[0], {
                    animation: 150,
                    onEnd: () => {
                        const newOrder = [];
                        $wrapper.find('.upload-preview .selected-img').each(function() {
                            const id = parseInt($(this).data('id'));
                            const item = selectedImages.find(i => i.id === id);
                            if (item) newOrder.push(item);
                        });
                        selectedImages = newOrder;
                        $input.val(JSON.stringify(selectedImages));
                    }
                });
            }

            $('#{{ $uid }}_delete_btn').on('click', function() {
                selectedImages = [];
                renderSelected();
            });

            $('#{{ $uid }}_search_input').on('input', function() {
                const keyword = $(this).val();
                loadImages(1, keyword);
            });

            renderSelected();
        });
    </script>
@endPushOnce
