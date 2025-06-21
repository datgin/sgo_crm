function submitForm(formId, successCallback, url = null, errorCallback = null) {
    $(formId).on("submit", function (e) {
        e.preventDefault();

        const $form = $(this);
        const $btn = $form
            .find('button[type="submit"], #submitRequestBtn')
            .first();
        const originalText = $btn.html();

        $btn.prop("disabled", true).html(
            '<i class="fas fa-spinner fa-pulse"></i> Đang gửi...'
        );

        // ✅ Validate toàn bộ form dùng formValidator
        if (
            typeof formValidator !== "undefined" &&
            typeof formValidator.validate === "function"
        ) {
            if (!formValidator.validate()) {
                $btn.prop("disabled", false).html(originalText);
                return;
            }
        }

        // ✅ Cập nhật dữ liệu từ CKEditor nếu có
        if (typeof CKEDITOR !== "undefined") {
            for (const instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }

        const formData = new FormData(this);

        // ✅ Xóa dấu chấm trong các input có class `format-price`
        $form.find(".format-price").each(function () {
            const name = $(this).attr("name");
            if (!name) return; // bỏ qua nếu không có name
            const raw = $(this).val().replace(/\./g, "");
            formData.set(name, raw); // Ghi đè vào FormData
        });

        $.ajax({
            url: url || window.location.href,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                $("#loadingOverlay").show();
            },
            success: function (response) {
                if (typeof successCallback === "function") {
                    successCallback(response);
                }
            },
            error: function (xhr) {
                if (
                    xhr.status === 403 &&
                    xhr.getResponseHeader("Content-Type")?.includes("text/html")
                ) {
                    document.open();
                    document.write(xhr.responseText);
                    document.close();
                    return;
                }

                if (typeof errorCallback === "function") {
                    errorCallback(xhr);
                }

                datgin?.error(
                    xhr.responseJSON?.message ||
                        "Đã có lỗi xảy ra, vui lòng thử lại sau!"
                );
            },
            complete: function () {
                $("#loadingOverlay").hide();
                $btn.prop("disabled", false).html(originalText);
            },
        });
    });
}

function formatDate(dateString, format = "DD-MM-YYYY") {
    if (!dateString)
        return '<small class="text-muted">Chưa cập nhật...</small>';
    return dayjs(dateString).format(format);
}

function formatToVietnameseCurrency(value) {
    value = value.replace(/[^\d]/g, "");
    if (value === "") return "";
    return new Intl.NumberFormat("vi-VN").format(value);
}

$(document).on("input", ".format-price", function () {
    let cursorPos = this.selectionStart;
    let originalLength = this.value.length;

    this.value = formatToVietnameseCurrency(this.value);

    // Giữ lại vị trí con trỏ khi nhập
    let newLength = this.value.length;
    this.setSelectionRange(
        cursorPos + (newLength - originalLength),
        cursorPos + (newLength - originalLength)
    );
});
