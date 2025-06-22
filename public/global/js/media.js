let allImages = []; // biến lưu danh sách ảnh
let tempSelectedImages = {}; // biến lưu nhưng ảnh đã được chọn
let selectedIdsForDelete = new Set(); // biến lưu id cần xóa
let selectedImages = {};

window.mediaPopup = {
    currentUid: null,
    multiple: false,
    open(e, uid, multiple) {
        if (
            $(e.target).closest(
                ".selected-img, .btn-remove-img, .btn-preview-img"
            ).length
        )
            return;

        this.currentUid = uid;
        this.multiple = multiple;

        if (!selectedImages[uid]) {
            selectedImages[uid] = new Map();
        }

        $("[data-detail]").html(
            '<div class="text-muted fst-italic">Chọn ảnh để xem thông tin</div>'
        );

        if (selectedIdsForDelete.size > 0) {
            $("#delete_btn").removeClass("d-none");
        } else {
            $("#delete_btn").addClass("d-none");
        }

        $("#media_popup").show().addClass("show");

        loadImages();
    },

    close() {
        $("#media_popup").hide().removeClass("show");
    },

    upload() {
        const $uploadInput = $("#popup_upload_input");
        $uploadInput.trigger("click");

        $uploadInput.off("change").on("change", (e) => {
            const files = e.target.files;
            if (!files.length) return;

            const formData = new FormData();
            $.each(files, (i, file) => formData.append("file[]", file));

            $.ajax({
                url: "/media/upload",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: () => {
                    loadImages();
                    $uploadInput.val("");
                },
                error: () => alert("Không thể upload ảnh"),
            });
        });
    },
    selectImage(el) {
        const $el = $(el);
        const id = parseInt($el.data("id"));

        const image = allImages.find((img) => img.id === id);

        if (!image) return;

        const $list = $("div[data-list]");
        const $detail = $("[data-detail]");

        const allowMultiple = this.multiple;

        if (allowMultiple) {
            if (selectedIdsForDelete.has(id)) {
                selectedIdsForDelete.delete(id);

                // Xóa khỏi tempSelectedImages
                for (const [key, path] of Object.entries(tempSelectedImages)) {
                    if (path === image.path) {
                        delete tempSelectedImages[key];
                        break;
                    }
                }

                $el.removeClass("active");
                $el.find(".selected-icon").addClass("d-none");
            } else {
                const uniqueId =
                    Date.now().toString() + Math.floor(Math.random() * 1000000);
                tempSelectedImages[uniqueId] = image.path;

                $el.addClass("active");
                $el.find(".selected-icon").removeClass("d-none");

                // Thêm id vào Set
                selectedIdsForDelete.add(id);
            }
        } else {
            // Chế độ chọn 1 ảnh
            $list.find(".img-select").each(function () {
                $(this).removeClass("active");
                $(this).find(".selected-icon").addClass("d-none");
            });

            // Xóa toàn bộ id cũ trước khi chọn cái mới
            selectedIdsForDelete.clear();
            tempSelectedImages = {};

            const uniqueId =
                Date.now().toString() + Math.floor(Math.random() * 1000000);

            tempSelectedImages[uniqueId] = image.path;

            $el.addClass("active");
            $el.find(".selected-icon").removeClass("d-none");

            // Thêm id mới vào Set
            selectedIdsForDelete.add(id);
        }

        // Cập nhật preview
        if (image && $detail.length) {
            $detail.html(`
                <div class="border rounded p-2 mb-2" style="width: 100%; aspect-ratio: 1 / 1; display: flex; align-items: center; justify-content: center; background: #fff;">
                    <img src="${
                        image.path
                    }" class="mw-100 mh-100 object-fit-contain" alt="">
                </div>
                <div class="text-break small" style="overflow-y: auto; max-height: 60vh;">
                    <p><strong>Tên:</strong> ${image.name || "(Không có)"}</p>
                    <div class="d-flex align-items-center mb-3" style="gap: 0.5rem;">
                        <strong class="flex-shrink-0">URL:</strong>
                        <div class="flex-grow-1 position-relative" style="overflow: hidden;">
                            <code class="d-block text-truncate pe-4" style="white-space: nowrap;" id="media_url">
                                ${image.path}
                            </code>
                            <i class="fas fa-copy position-absolute top-0 end-0 text-muted copy-icon" style="cursor: pointer;" title="Sao chép"></i>
                        </div>
                    </div>
                    <p><strong>Kích thước:</strong> ${
                        image.size || "..."
                    } KB</p>
                    <p><strong>Ngày tải lên:</strong> ${
                        image.uploaded_at || "..."
                    }</p>
                    <p><strong>Chiều rộng:</strong> ${
                        image.width || "..."
                    } px</p>
                    <p><strong>Chiều cao:</strong> ${
                        image.height || "..."
                    } px</p>
                </div>
            `);
        }

        // // Hiển thị hoặc ẩn nút Xóa
        if (selectedIdsForDelete.size > 0) {
            $("#delete_btn").removeClass("d-none");
        } else {
            $("#delete_btn").addClass("d-none");
        }
    },
    delete() {
        Swal.fire({
            title: "Bạn có chắc chắn?",
            text: "Hành động này không thể hoàn tác!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Đồng ý!",
            cancelButtonText: "Hủy",
        }).then((result) => {
            if (result.isConfirmed) {
                const ids = Array.from(selectedIdsForDelete);

                if (ids.length === 0) {
                    datgin.warning("Vui lòng chọn ít nhất một ảnh để xoá.");
                    return;
                }

                $.ajax({
                    url: "/media/destroy",
                    method: "DELETE",
                    data: {
                        ids,
                    },
                    success: function (response) {
                        datgin.success(
                            response.message || "Đã xoá ảnh thành công."
                        );

                        selectedIdsForDelete.clear();
                        tempSelectedImages = {};

                        $("#delete_btn").addClass("d-none");
                        loadImages();

                        $("[data-detail]").html(
                            '<div class="text-muted fst-italic">Chọn ảnh để xem thông tin</div>'
                        );
                    },
                    error: function (xhr) {
                        let msg = "Xoá ảnh thất bại.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        datgin.error(msg);
                    },
                });
            }
        });
    },
    handleSelect: internalHandleSelect,
};

function internalHandleSelect() {
    const uid = window.mediaPopup.currentUid;
    const multiple = window.mediaPopup.multiple;
    const selectedImg = selectedImages[uid];

    const hasTempImages = Object.keys(tempSelectedImages).length > 0;

    // Nếu chỉ chọn 1 ảnh (multiple = false) và có ảnh tạm được chọn
    if (!multiple && hasTempImages) {
        selectedImg.clear(); // Xoá toàn bộ ảnh cũ

        // Thêm chỉ 1 ảnh đầu tiên từ temp vào selectedImg
        const [firstId, firstPath] = Object.entries(tempSelectedImages)[0];
        selectedImg.set(firstId, firstPath);
    }

    // Nếu multiple = true hoặc không có ảnh tạm → thêm như cũ
    if (multiple && hasTempImages) {
        for (const [id, path] of Object.entries(tempSelectedImages)) {
            const exists = Array.from(selectedImg.values()).includes(path);
            if (!exists) {
                selectedImg.set(id, path);
            }
        }
    }

    // Render giao diện
    const $preview = $(`#${uid}_upload-preview`);
    const $placeholder = $(`#${uid}_placeholder_text`);
    const $inputWrapper = $(`#${uid}_selected-images-input`);

    const images = Array.from(selectedImg.entries()).map(([id, path]) => ({
        id,
        path,
    }));

    let html = "";
    $.each(images, function (i, img) {
        html += `
            <div data-uid="${uid}" data-id="${img.id}" class="position-relative selected-img" style="width: 100px; height: 100px; flex-shrink: 0;">
                <div class="w-100 h-100 position-relative overflow-hidden rounded">
                    <img src="${img.path}" data-path="${img.path}" class="img-thumbnail w-100 h-100 object-fit-cover rounded">
                    <div class="overlay-hover-image position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 gap-2 justify-content-center align-items-center" style="display: none;">
                        <a href="${img.path}" data-lightbox="preview-${uid}" class="btn-preview-img">
                            <i class="fas fa-eye shadow fw-medium" title="Xem ảnh"></i>
                        </a>
                        <i class="far fa-trash-alt btn-remove-img shadow" title="Xoá ảnh"></i>
                    </div>
                </div>
            </div>
        `;
    });

    $preview.html(html);
    $placeholder.toggle(images.length === 0);

    // Tạo lại input hidden
    $inputWrapper.empty();
    images.forEach((img) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = multiple
            ? `${$inputWrapper.data("name")}[]`
            : $inputWrapper.data("name");
        input.value = img.path;
        input.dataset.id = img.id;
        input.classList.add("selected-images-input");
        $inputWrapper.append(input);
    });

    selectedIdsForDelete.clear();
    tempSelectedImages = {};
    window.mediaPopup.close();
}

function loadImages(page = 1, keyword = "") {
    const $list = $("div[data-list]");
    $list.html('<div class="text-muted p-4">Đang tải ảnh...</div>');

    $.ajax({
        url: `/media?page=${page}&search=${keyword}`,
        method: "GET",
        beforeSend: () => $("#loadingOverlay").show(),
        complete: () => $("#loadingOverlay").hide(),
        success: function (res) {
            allImages = res.data || [];

            if (!allImages.length) {
                $list.html(
                    '<div class="text-center fs-6 text-muted">Danh sách ảnh trống.</div>'
                );
                return;
            }

            const selectedImg =
                selectedImages[window.mediaPopup.currentUid] || new Map();

            let html = '<div class="row g-3 mb-3">';

            $.each(allImages, function (i, image) {
                html += `
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="ratio ratio-1x1 overflow-hidden position-relative img-select border rounded"
                            data-id="${image.id}"
                            data-path="${image.path}"
                            style="cursor: pointer;">
                            <img src="${image.path}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0" alt="">
                            <i class="fas fa-check-circle selected-icon text-primary position-absolute me-1 mt-1 d-none"></i>
                        </div>
                    </div>
                `;
            });

            html += "</div>"; // Đóng danh sách ảnh

            let paginationHtml = ""; // Khởi tạo biến ở ngoài để tránh mất
            if (res.pagination && res.pagination.last_page > 1) {
                const currentPage = res.pagination.current_page;
                const lastPage = res.pagination.last_page;

                paginationHtml += '<div class="custom-pagination">';

                const createBtn = (
                    page,
                    label,
                    disabled = false,
                    isActive = false
                ) => {
                    return `
                        <li class="page-item ${disabled ? "disabled" : ""} ${
                        isActive ? "active" : ""
                    }">
                            <a href="#" class="page-link" data-page="${page}">${label}</a>
                        </li>
                    `;
                };

                paginationHtml += createBtn(1, "&laquo;", currentPage === 1);
                paginationHtml += createBtn(
                    currentPage - 1,
                    "&lsaquo;",
                    currentPage === 1
                );

                let start = Math.max(currentPage - 2, 1);
                let end = Math.min(currentPage + 2, lastPage);
                for (let i = start; i <= end; i++) {
                    paginationHtml += createBtn(i, i, false, i === currentPage);
                }

                paginationHtml += createBtn(
                    currentPage + 1,
                    "&rsaquo;",
                    currentPage === lastPage
                );
                paginationHtml += createBtn(
                    lastPage,
                    "&raquo;",
                    currentPage === lastPage
                );

                paginationHtml += "</div>";
            }

            // ⚠️ Gán cả ảnh + phân trang
            $list.html(html + paginationHtml);
        },
        error: function () {
            $list.html('<div class="text-danger">Không thể tải ảnh</div>');
        },
    });
}

// ✅ Gắn sự kiện chọn ảnh sau khi DOM load xong (delegated)
$(document).on("click", ".img-select", function () {
    window.mediaPopup.selectImage(this);
});

$(document)
    .off("click", ".copy-icon")
    .on("click", ".copy-icon", function () {
        const url = $(this).siblings("code").text().trim();

        navigator.clipboard.writeText(url).then(() => {
            $(this).removeClass("text-muted").addClass("text-success");
            $(this).attr("title", "Đã sao chép!");
            datgin.success("Đã sao chép!");

            setTimeout(() => {
                $(this).removeClass("text-success").addClass("text-muted");
                $(this).attr("title", "Sao chép");
            }, 1500);
        });
    });

$(document).on("click", ".btn-open-media", function (e) {
    const uid = $(this).data("uid");
    const multiple = $(this).data("multiple");
    window.mediaPopup.open(e, uid, multiple);
});

$(document).on("click", ".btn-remove-img", function (e) {
    e.stopPropagation();
    const $imgBox = $(this).closest(".selected-img");
    const $id = $imgBox.data("id");
    const $uid = $imgBox.data("uid");

    const selectedImg = selectedImages[$uid];

    for (const [id] of selectedImg.entries()) {
        if (id === $id) {
            selectedImg.delete(id);
            break;
        }
    }

    $imgBox.remove();

    $(`#${$uid}_selected-images-input input[data-id="${$id}"]`).remove();

    // Kiểm tra nếu đã xóa hết => hiện placeholder
    const $placeholder = $(`#${$uid}_placeholder_text`);

    const isEmpty = selectedImg.size === 0;

    $placeholder.toggle(isEmpty);
});

let debounceTimer = null;

$(document).on("input", "#popup_search_input", function () {
    const keyword = $(this).val();

    // Hủy timer cũ (nếu có)
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    // Tạo timer mới (300ms)
    debounceTimer = setTimeout(() => {
        loadImages(1, keyword);
    }, 300);
});

$(document).on("click", ".custom-pagination .page-link", function (e) {
    e.preventDefault();

    const page = parseInt($(this).data("page"));
    if (!page || $(this).closest(".page-item").hasClass("disabled")) return;

    const keyword = $("#popup_search_input").val();
    loadImages(page, keyword);
});
