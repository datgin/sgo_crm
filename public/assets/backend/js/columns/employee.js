const columns = [
    {
        data: "full_name_code",
        name: "full_name_code",
        title: "",
        orderable: false,
        searchable: false,
        width: "5%",
    },
    {
        data: "DT_RowIndex",
        name: "DT_RowIndex",
        title: "SST",
        orderable: false,
        searchable: false,
        width: "4%",
    },
    {
        data: "full_name",
        name: "full_name",
        title: "Họ tên",
    },
    {
        data: "code",
        name: "code",
        title: "Mã nhân viên",
    },
    {
        data: "avatar",
        name: "avatar",
        title: "Ảnh",
        orderable: false,
        searchable: false,
        render: function (data) {
            return `<img width="40px" src="${data}"/>`;
        },
    },
    {
        data: "position",
        name: "position",
        title: "Vị trí",
        orderable: false,
        searchable: false,
    },
    {
        data: "phone",
        name: "phone",
        title: "Số điện thoại",
    },
    {
        data: "address",
        name: "address",
        title: "Địa chỉ tạm trú",
    },
    {
        data: "birthday",
        name: "birthday",
        title: "Ngày sinh",
        render: function (data, type, row) {
            return formatDate(data, "DD-MM-YYYY");
        },
    },
    {
        data: "age",
        name: "age",
        title: "Số tuổi",
        orderable: false,
        searchable: false,
    },
    {
        data: "gender",
        name: "gender",
        title: "Giới tính",
        orderable: false,
        searchable: false,
        render: function (data) {
            const genderMap = {
                male: { label: "Nam", class: "primary" },
                female: { label: "Nữ", class: "danger" },
                other: { label: "Khác", class: "secondary" },
            };

            const gender = genderMap[data];
            return gender
                ? `<span class="badge bg-${gender.class}">${gender.label}</span>`
                : "";
        },
    },
    {
        data: "cccd",
        name: "cccd",
        title: "Số CCCD",
        searchable: false,
        render: function (data, type, row, meta) {
            if (!data) return "";
            return data.substring(0, 2) + "x".repeat(data.length - 2);
        },
        orderable: false,
    },
    {
        data: "cccd_issued_date",
        name: "cccd_issued_date",
        title: "ngày cấp CCCD",
        render: function (data, type, row) {
            return formatDate(data, "DD-MM-YYYY");
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "education_level",
        name: "education_level",
        title: "Trình độ học vấn",
        orderable: false,
        searchable: false,
    },
    {
        data: "university_start_date",
        name: "university_start_date",
        title: "Thời gian vào ĐH",
        render: function (data) {
            return formatDate(data, "DD-MM-YYYY");
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "university_end_date",
        name: "university_end_date",
        title: "Kết thúc ĐH",
        render: function (data) {
            return formatDate(data, "DD-MM-YYYY");
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "days_left_for_university",
        name: "days_left_for_university",
        title: "Số ngày còn lại của ĐH",
        orderable: false,
        searchable: false,
    },
    {
        data: "seniority",
        name: "seniority",
        title: "Thâm niên",
        orderable: false,
        searchable: false,
    },
    {
        data: "contract_code",
        name: "contract_code",
        title: "Số hợp đồng",
        orderable: false,
        searchable: false,
    },
    {
        data: "contract_type",
        name: "contract_type",
        title: "Loại hợp đồng",
        orderable: false,
        searchable: false,
    },
    {
        data: "contract_link",
        name: "contract_link",
        title: "Link hợp đồng",
        class: "contract_link",
        orderable: false,
        searchable: false,
    },
    {
        data: "employment_status",
        name: "employment_status",
        title: "Tình trạng làm việc",
        orderable: false,
        searchable: false,
    },
    {
        data: "resignation_date",
        name: "resignation_date",
        title: "Ngày nghỉ việc",
        render: function (data) {
            return formatDate(data, "DD-MM-YYYY");
        },
        orderable: false,
        searchable: false,
    },
    {
        data: "notes",
        name: "notes",
        title: "Ghi chú",
        render: function (data) {
            return data ?? '<span class="text-muted">NA</span>';
        },
        orderable: false,
        searchable: false,
    },
];
