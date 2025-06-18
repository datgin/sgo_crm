const dataTables = (
    api,
    {
        filters = {},
        isOperation = true,
        hasCheckbox = true,
        hasDateRange = false,
        hasDtControl = false,
        fixedColumns = null,
        scrollX = true,
        onInitComplete = null,
        onDrawCallback = null,
        tableId = "#myTable",
    } = {}
) => {
    const $table = $(tableId);
    $table.empty(); // clear old headers if re-render

    // Generate <thead>
    let thead = "<thead><tr>";
    if (hasDtControl) thead += "<th></th>";

    if (hasCheckbox)
        thead +=
            '<th><input type="checkbox" id="selectAll" class="form-check-input" /></th>';
    thead += columns
        .filter((col) => col.className !== "dt-control")
        .map((col) => `<th>${col.title || ""}</th>`)
        .join("");
    if (isOperation) thead += "<th>Hành động</th>";
    thead += "</tr></thead>";
    $table.append(thead);

    // Build columns
    const baseColumns = columns.filter((col) => col.className !== "dt-control");
    const finalColumns = [];

    if (hasDtControl) {
        finalColumns.push({
            className: "dt-control",
            orderable: false,
            data: null,
            defaultContent: "",
        });
    }

    if (hasCheckbox) {
        finalColumns.push({
            data: "checkbox",
            name: "checkbox",
            orderable: false,
            searchable: false,
            width: "5px",
            className: "text-center",
        });
    }

    finalColumns.push(...baseColumns);

    if (isOperation) {
        finalColumns.push({
            data: "operations",
            name: "operations",
            title: "Hành động",
            orderable: false,
            searchable: false,
            className: "text-center",
            width: "8%",
        });
    }

    // DataTable options
    const options = {
        processing: true,
        serverSide: true,
        ajax: {
            url: api,
            data: function (d) {
                Object.keys(filters).forEach((key) => {
                    const val = $(`#filter-${key}`).val();
                    if (val) d[key] = val;
                });

                const dateRange = $("#dateRangePicker").val();
                if (dateRange) {
                    const [startDate, endDate] = dateRange.split(" - ");
                    d.start_date = moment(startDate, "DD/MM/YYYY").format(
                        "YYYY-MM-DD"
                    );
                    d.end_date = moment(endDate, "DD/MM/YYYY").format(
                        "YYYY-MM-DD"
                    );
                }
            },
        },
        columns: finalColumns,
        order: [],
        createdRow: function (row, data) {
            $(row).attr("data-id", data.id);
        },
        initComplete: function () {
            if (typeof onInitComplete === "function")
                onInitComplete(this.api());
        },
        drawCallback: function () {
            if (typeof onDrawCallback === "function")
                onDrawCallback(this.api());
        },
        layout: {
            topEnd: {
                search: {
                    placeholder: "Tìm kiếm...",
                },
            },
        },
        language: {
            lengthMenu: "Hiển thị _MENU_ bản ghi mỗi trang",
            zeroRecords: "Không tìm thấy kết quả phù hợp",
            info: "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ bản ghi",
            infoEmpty: "Không có bản ghi nào",
            infoFiltered: "(lọc từ tổng số _MAX_ bản ghi)",
            search: "Tìm kiếm:",
            paginate: {
                first: "Đầu",
                last: "Cuối",
                next: "Sau",
                previous: "Trước",
            },
        },
    };

    if (scrollX) {
        options.scrollX = true;
        options.scrollCollapse = true;
    }

    if (fixedColumns) {
        options.fixedColumns = {
            leftColumns: fixedColumns.left || 0,
            rightColumns: fixedColumns.right || 0,
        };
    }

    const table = $table.DataTable(options);

    // Date range filter
    if (hasDateRange) {
        const $target = $(".dt-layout-cell.dt-layout-start .dt-length");
        const datePickerHtml = `
            <div class="d-flex align-items-center mb-2 ms-2">
                <input type="text" id="dateRangePicker" name="date_range" class="form-control form-control-sm" placeholder="Chọn khoảng ngày" />
            </div>`;
        $target.after(datePickerHtml);

        $("#dateRangePicker").daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: "Clear",
                applyLabel: "Áp dụng",
                format: "DD/MM/YYYY",
            },
        });

        $("#dateRangePicker").on("cancel.daterangepicker", function () {
            $(this).val("");
            table.ajax.reload();
        });

        $("#dateRangePicker").on(
            "apply.daterangepicker",
            function (ev, picker) {
                $(this).val(
                    `${picker.startDate.format(
                        "DD/MM/YYYY"
                    )} - ${picker.endDate.format("DD/MM/YYYY")}`
                );
                table.ajax.reload();
            }
        );
    }

    // Reload on filter change
    Object.keys(filters).forEach((key) => {
        $(`#filter-${key}`).on("change", () => table.ajax.reload());
    });

    return table;
};
