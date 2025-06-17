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
        const [day, month, year] = date.split("/");
        if (format === "d/m/Y") {
            if (day.length === 2 && month.length === 2 && year.length === 4) {
                const parsedDate = new Date(`${year}-${month}-${day}`);
                return (
                    parsedDate.getDate() == day &&
                    parsedDate.getMonth() + 1 == month &&
                    parsedDate.getFullYear() == year
                );
            }
        }
        return false;
    };

    function validate(rules, attributes) {
        let isValid = true;
        for (const [field, ruleString] of Object.entries(rules)) {
            const inputElement = $(`[name="${field}"]`);
            const value = inputElement.val();
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
                array: `${fieldLabel} phải là một mảng.`,
                url: `${fieldLabel} không đúng định dạng URL.`,
                in: (values) =>
                    `${fieldLabel} phải là một trong: ${values.join(", ")}.`,
            };

            for (let rule of fieldRules) {
                if (rule === "required") {
                    if (!value || value.trim() === "") {
                        errorMessage = defaultMessages.required;
                        break;
                    }
                } else if (rule === "email") {
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailPattern.test(value)) {
                        errorMessage = defaultMessages.email;
                        break;
                    }
                } else if (rule.startsWith("min")) {
                    const min = parseInt(rule.split(":")[1]);
                    if (value.length < min) {
                        errorMessage = defaultMessages.min(min);
                        break;
                    }
                } else if (rule.startsWith("max")) {
                    const max = parseInt(rule.split(":")[1]);
                    if (value.length > max) {
                        errorMessage = defaultMessages.max(max);
                        break;
                    }
                } else if (rule === "numeric") {
                    if (!/^\d+$/.test(value)) {
                        errorMessage = defaultMessages.numeric;
                        break;
                    }
                } else if (rule === "integer") {
                    if (!/^[-+]?\d+$/.test(value)) {
                        errorMessage = defaultMessages.integer;
                        break;
                    }
                } else if (rule === "alpha") {
                    if (!/^[a-zA-Z]+$/.test(value)) {
                        errorMessage = defaultMessages.alpha;
                        break;
                    }
                } else if (rule === "alpha_num") {
                    if (!/^[a-zA-Z0-9]+$/.test(value)) {
                        errorMessage = defaultMessages.alpha_num;
                        break;
                    }
                } else if (rule.startsWith("regex")) {
                    const pattern = rule.split(":")[1];
                    const regex = new RegExp(pattern);
                    if (!regex.test(value)) {
                        errorMessage = defaultMessages.regex;
                        break;
                    }
                } else if (rule === "date") {
                    if (isNaN(Date.parse(value))) {
                        errorMessage = defaultMessages.date;
                        break;
                    }
                } else if (rule.startsWith("date_format")) {
                    const format = rule.split(":")[1];
                    if (!isValidDateFormat(value, format)) {
                        errorMessage = defaultMessages.date_format(format);
                        break;
                    }
                } else if (rule.startsWith("before")) {
                    const date = rule.split(":")[1];
                    if (new Date(value) >= new Date(date)) {
                        errorMessage = defaultMessages.before(date);
                        break;
                    }
                } else if (rule === "after_today") {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const [day, month, year] = value.split("/");
                    const inputDate = new Date(`${year}-${month}-${day}`);
                    if (inputDate <= today) {
                        errorMessage = defaultMessages.after_today;
                        break;
                    }
                } else if (rule === "array") {
                    if (!Array.isArray(value)) {
                        errorMessage = defaultMessages.array;
                        break;
                    }
                } else if (rule === "url") {
                    const urlPattern = /^(https?:\/\/[^\s$.?#].[^\s]*)$/;
                    if (!urlPattern.test(value)) {
                        errorMessage = defaultMessages.url;
                        break;
                    }
                } else if (rule.startsWith("in")) {
                    const values = rule.split(":")[1].split(",");
                    if (!values.includes(value)) {
                        errorMessage = defaultMessages.in(values);
                        break;
                    }
                }
            }

            if (errorMessage) {
                inputElement
                    .next(".error-message")
                    .text(errorMessage)
                    .css("display", "block");
                isValid = false;
            } else {
                inputElement
                    .next(".error-message")
                    .text("")
                    .css("display", "none");
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
        "input change",
        "input, textarea, select",
        debounce(function () {
            const fieldName = this.name;
            const rules = formValidator.rules;
            const attrs = formValidator.attributes;

            if (rules[fieldName]) {
                validate({ [fieldName]: rules[fieldName] }, attrs);
            }
        }, 250)
    );
});
