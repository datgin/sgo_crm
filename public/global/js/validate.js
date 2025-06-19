$(document).ready(function () {
    // Hàm debounce giúp giới hạn số lần gọi sự kiện
    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    const isValidDateFormat = (date, format) => {
        const delimiters = ["/", "-", "."];
        const delimiter = delimiters.find((d) => format.includes(d));
        if (!delimiter) return false;

        const dateParts = date.split(delimiter);
        const formatParts = format.split(delimiter);
        if (dateParts.length !== 3 || formatParts.length !== 3) return false;

        const day = dateParts[formatParts.indexOf("d")];
        const month = dateParts[formatParts.indexOf("m")];
        const year = dateParts[formatParts.indexOf("Y")];

        if (!day || !month || !year) return false;
        if (day.length !== 2 || month.length !== 2 || year.length !== 4)
            return false;

        const parsedDate = new Date(`${year}-${month}-${day}`);
        return (
            parsedDate.getDate() == day &&
            parsedDate.getMonth() + 1 == month &&
            parsedDate.getFullYear() == year
        );
    };

    function parseDateByFormat(value, format) {
        if (!value || !format) return null;

        const parts = value.split(/[^0-9]/); // tách theo ký tự phân cách như -, /
        const formatParts = format.split(/[^a-zA-Z]/); // tách theo ký tự phân cách

        let day, month, year;

        for (let i = 0; i < formatParts.length; i++) {
            const part = formatParts[i].toLowerCase();
            const val = parseInt(parts[i]);
            if (part === "d" || part === "dd") day = val;
            else if (part === "m" || part === "mm") month = val - 1;
            else if (part === "y" || part === "yy" || part === "yyyy")
                year = val;
        }

        if (!day || month === undefined || !year) return null;

        const date = new Date(year, month, day);
        return isNaN(date.getTime()) ? null : date;
    }

    function validate(rules, attributes) {
        let isValid = true;

        for (const [field, ruleString] of Object.entries(rules)) {
            const inputElement = $(`[name="${field}"], [name="${field}[]"]`);

            const inputType = inputElement.attr("type");
            const isFile = inputType === "file";
            const file = inputElement[0]?.files?.[0];
            const value = isFile ? file : inputElement.val();

            const fieldLabel =
                attributes[field] ||
                field
                    .split("_")
                    .map((w, i) => (i === 0 ? capitalize(w) : w))
                    .join(" ");

            const fieldRules = ruleString.split("|");
            let errorMessage = "";

            const defaultMessages = {
                required: `${fieldLabel} là bắt buộc.`,
                email: `${fieldLabel} không đúng định dạng.`,
                min: (min) => `${fieldLabel} phải có ít nhất ${min} ký tự.`,
                max: (max) => `${fieldLabel} không được vượt quá ${max} ký tự.`,
                numeric: `${fieldLabel} chỉ chấp nhận số.`,
                integer: `${fieldLabel} phải là số nguyên.`,
                alpha: `${fieldLabel} chỉ chấp nhận ký tự chữ.`,
                alpha_num: `${fieldLabel} chỉ chấp nhận ký tự chữ và số.`,
                regex: `${fieldLabel} không đúng định dạng.`,
                date: `${fieldLabel} không phải là ngày hợp lệ.`,
                date_format: (format) =>
                    `${fieldLabel} phải có định dạng ${format}.`,
                before: (date) => `${fieldLabel} phải trước ngày ${date}.`,
                after_today: `${fieldLabel} phải sau ngày hôm nay.`,
                before_today: `${fieldLabel} phải trước ngày hôm nay.`,
                array: `${fieldLabel} phải là một mảng.`,
                url: `${fieldLabel} không đúng định dạng URL.`,
                in: `${fieldLabel} đã chọn không hợp lệ`,
                file: `${fieldLabel} không phải là file hợp lệ.`,
                mimes: (types) =>
                    `${fieldLabel} phải có định dạng: ${types.join(", ")}.`,
                max_size: (size) =>
                    `${fieldLabel} không được vượt quá ${size} KB.`,
                digits_between: (min, max) =>
                    `${fieldLabel} phải có độ dài từ ${min} đến ${max} chữ số.`,
                before_or_equal: (date) =>
                    `${fieldLabel} phải trước hoặc bằng ngày ${date}.`,
                after_or_equal: (date) =>
                    `${fieldLabel} phải sau hoặc bằng ${date}.`,
            };

            if (
                fieldRules.includes("nullable") &&
                (!value || value.toString().trim() === "")
            ) {
                errorMessage = "";
                continue;
            }

            for (let rule of fieldRules) {
                const [ruleName, ruleValue] = rule.includes(":")
                    ? rule.split(":", 2)
                    : [rule, null];

                if (ruleName === "required") {
                    if (isFile ? !file : !value || value.trim() === "") {
                        errorMessage = defaultMessages.required;
                        break;
                    }
                } else if (ruleName === "email") {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(value)) {
                        errorMessage = defaultMessages.email;
                        break;
                    }
                } else if (ruleName === "min") {
                    const min = parseInt(ruleValue);
                    if (value.length < min) {
                        errorMessage = defaultMessages.min(min);
                        break;
                    }
                } else if (ruleName === "max") {
                    const max = parseInt(ruleValue);
                    if (value.length > max) {
                        errorMessage = defaultMessages.max(max);
                        break;
                    }
                } else if (ruleName === "numeric") {
                    if (!/^\d+$/.test(value)) {
                        errorMessage = defaultMessages.numeric;
                        break;
                    }
                } else if (ruleName === "integer") {
                    if (!/^[-+]?\d+$/.test(value)) {
                        errorMessage = defaultMessages.integer;
                        break;
                    }
                } else if (ruleName === "alpha") {
                    if (!/^[a-zA-Z]+$/.test(value)) {
                        errorMessage = defaultMessages.alpha;
                        break;
                    }
                } else if (ruleName === "alpha_num") {
                    if (!/^[a-zA-Z0-9]+$/.test(value)) {
                        errorMessage = defaultMessages.alpha_num;
                        break;
                    }
                } else if (ruleName === "regex") {
                    const regex = new RegExp(ruleValue);
                    if (!regex.test(value)) {
                        errorMessage = defaultMessages.regex;
                        break;
                    }
                } else if (ruleName === "date") {
                    if (isNaN(Date.parse(value))) {
                        errorMessage = defaultMessages.date;
                        break;
                    }
                } else if (ruleName === "date_format") {
                    if (!isValidDateFormat(value, ruleValue)) {
                        errorMessage = defaultMessages.date_format(ruleValue);
                        break;
                    }
                } else if (ruleName === "before") {
                    if (new Date(value) >= new Date(ruleValue)) {
                        errorMessage = defaultMessages.before(ruleValue);
                        break;
                    }
                } else if (ruleName === "after_today") {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const [day, month, year] = value.split("/");
                    const inputDate = new Date(`${year}-${month}-${day}`);
                    if (inputDate <= today) {
                        errorMessage = defaultMessages.after_today;
                        break;
                    }
                } else if (ruleName === "before_today") {
                    const formatRule = fieldRules.find((r) =>
                        r.startsWith("date_format:")
                    );

                    const format = formatRule
                        ? formatRule.split(":")[1]
                        : "Y-m-d";
                    const inputDate = parseDateByFormat(value, format);

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    if (!inputDate || inputDate >= today) {
                        errorMessage = defaultMessages.before_today;
                        break;
                    }
                } else if (ruleName === "array") {
                    if (!Array.isArray(value)) {
                        errorMessage = defaultMessages.array;
                        break;
                    }
                } else if (ruleName === "url") {
                    const urlPattern = /^(https?:\/\/[^\s$.?#].[^\s]*)$/;
                    if (!urlPattern.test(value)) {
                        errorMessage = defaultMessages.url;
                        break;
                    }
                } else if (ruleName === "in") {
                    const values = ruleValue.split(",");
                    if (!values.includes(value)) {
                        errorMessage = defaultMessages.in(values);
                        break;
                    }
                } else if (ruleName === "file") {
                    if (!file) {
                        errorMessage = defaultMessages.file;
                        break;
                    }
                } else if (ruleName === "mimes") {
                    const allowedTypes = ruleValue.split(",");
                    if (file) {
                        const extension = file.name
                            .split(".")
                            .pop()
                            .toLowerCase();
                        if (!allowedTypes.includes(extension)) {
                            errorMessage = defaultMessages.mimes(allowedTypes);
                            break;
                        }
                    }
                } else if (ruleName === "max_size") {
                    const maxSize = parseInt(ruleValue);
                    if (file) {
                        const sizeInKB = file.size / 1024;
                        if (sizeInKB > maxSize) {
                            errorMessage = defaultMessages.max_size(maxSize);
                            break;
                        }
                    }
                } else if (ruleName === "digits_between") {
                    const [min, max] = ruleValue.split(",");
                    const pattern = /^\d+$/;
                    if (
                        !pattern.test(value) ||
                        value.length < min ||
                        value.length > max
                    ) {
                        errorMessage = defaultMessages.digits_between(min, max);
                        break;
                    }
                } else if (ruleName === "before_or_equal") {
                    const formatRule = fieldRules.find((r) =>
                        r.startsWith("date_format:")
                    );
                    const format = formatRule
                        ? formatRule.split(":")[1]
                        : "yyyy-mm-dd";

                    const inputDate = formatRule
                        ? parseDateByFormat(value, format)
                        : new Date(value);

                    let compareDate;
                    if (ruleValue === "today") {
                        compareDate = new Date();
                        compareDate.setHours(0, 0, 0, 0);
                    } else {
                        compareDate = formatRule
                            ? parseDateByFormat(ruleValue, format)
                            : new Date(ruleValue);
                    }

                    if (!inputDate || !compareDate || inputDate > compareDate) {
                        const displayDate =
                            ruleValue === "today"
                                ? new Date().toLocaleDateString("vi-VN")
                                : ruleValue;
                        errorMessage =
                            defaultMessages.before_or_equal(displayDate);
                        break;
                    }
                } else if (ruleName === "after_or_equal") {
                    const formatRule = fieldRules.find((r) =>
                        r.startsWith("date_format:")
                    );
                    const format = formatRule
                        ? formatRule.split(":")[1]
                        : "yyyy-mm-dd";

                    const inputDate = formatRule
                        ? parseDateByFormat(value, format)
                        : new Date(value);

                    let compareValue;

                    if (ruleValue === "today") {
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        compareValue = today;
                    } else {
                        const compareInputVal = $(
                            `[name="${ruleValue}"]`
                        ).val();
                        compareValue = formatRule
                            ? parseDateByFormat(compareInputVal, format)
                            : new Date(compareInputVal);
                    }

                    if (
                        !inputDate ||
                        !compareValue ||
                        inputDate < compareValue
                    ) {
                        const displayDate =
                            ruleValue === "today"
                                ? new Date().toLocaleDateString("vi-VN")
                                : compareValue instanceof Date
                                ? compareValue.toLocaleDateString("vi-VN")
                                : compareValue;

                        errorMessage =
                            defaultMessages.after_or_equal(displayDate);
                        break;
                    }
                }
            }

            if (errorMessage) {
                $(`.error-message.${field}`)
                    .text(errorMessage)
                    .css("display", "block");

                isValid = false;
            } else {
                $(`.error-message.${field}`).text("").css("display", "none");
            }
        }

        return isValid;
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Gán validate toàn cục
    window.formValidator = {
        rules: {},
        attributes: {},
        set(rules, attributes = {}) {
            this.rules = rules;
            this.attributes = attributes;
        },
        validate(subset = null) {
            const rulesToCheck = subset
                ? Object.fromEntries(
                      Object.entries(this.rules).filter(([key]) =>
                          subset.includes(key)
                      )
                  )
                : this.rules;
            return validate(rulesToCheck, this.attributes);
        },
    };

    // Validate tự động khi gõ
    $(document).on(
        "input",
        "input:not([type=file]):not([type=checkbox]):not([type=radio]), textarea",
        debounce(function () {
            validateSingleField(this);
        }, 300)
    );

    // validate khi chọn select hoặc thay đổi radio/checkbox
    $(document).on(
        "change",
        "select, input[type=checkbox], input[type=radio], input[type=file]",
        function () {
            validateSingleField(this);
        }
    );

    function validateSingleField(inputEl) {
        const fieldName = inputEl.name;
        const rules = formValidator.rules;
        const attrs = formValidator.attributes;

        if (rules[fieldName]) {
            validate({ [fieldName]: rules[fieldName] }, attrs);
        }
    }
});
