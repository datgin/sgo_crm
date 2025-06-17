const columns = [
    {
        title: "Mã NV",
        dataIndex: "code",
        width: "20%",
    },
    {
        title: "Họ tên",
        dataIndex: "full_name",
        class: "text-center",
        render: function (row) {
            return `<a href="#">${row.full_name}</a>`;
        },
    },
    {
        title: "SĐT",
        dataIndex: "phone",
    },
];
