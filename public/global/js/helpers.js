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

        // ✅ Xóa dấu phẩy trong các input có class `usd-price-format`
        $(".usd-price-format").each(function () {
            const name = $(this).attr("name");
            const rawValue = $(this).val().replace(/,/g, "");
            formData.set(name, rawValue);
        });

        $.ajax({
            url: url || window.location.href,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#loadingSpinner").fadeIn();
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
                $("#loadingSpinner").fadeOut();
                $btn.prop("disabled", false).html(originalText);
            },
        });
    });
}
