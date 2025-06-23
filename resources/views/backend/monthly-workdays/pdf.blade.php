<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Bảng Thanh Toán Lương</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background-color: #dce6f1;
        }

        .no-border {
            border: none;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .sub-info {
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .footer-table {
            margin-top: 20px;
            width: 50%;
            font-size: 12px;
        }

        .footer-table th,
        .footer-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: right;
        }

        .footer-table th {
            text-align: left;
            background-color: #f0f0f0;
        }
    </style>
</head>

<body>

    <div class="sub-info">Tên đơn vị: {{ $setting->company_name }}</div>
    <div class="sub-info">Địa chỉ: {{ $setting->address }}</div>

    <h2>BẢNG THANH TOÁN LƯƠNG</h2>
    @php
        [$year, $month] = explode('-', $monthlyWorkday->month);
    @endphp

    <div class="sub-info">
        Tháng: {{ $month }} &nbsp;&nbsp;&nbsp;
        Năm: {{ $year }} &nbsp;&nbsp;&nbsp;
        Số ngày làm trong tháng: {{ $monthlyWorkday->workdays }}
    </div>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Mã NV</th>
                <th>Họ tên</th>
                <th>Bộ phận</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>NV01</td>
                <td>Nguyễn Văn A</td>
                <td>Kinh doanh</td>
            </tr>
        </tbody>
    </table>


    <table>
        <thead>
            <tr>

                <th rowspan="2">Lương cơ bản</th>
                <th colspan="3">Số ngày công</th>


                <th rowspan="2">Lương</th>
            </tr>
            <tr>
                <th>Ngày thường</th>
                <th>Làm CN</th>
                <th>Ngày lễ</th>
            </tr>
        </thead>
        <tbody>

            @forelse ($salaryRangesForMonth as $item)
                <tr>

                    <td>{{ formatPrice($item['salary'], true) }}</td>
                    <td>{{ $item['working_days_in_month'] }}</td>
                    <td>0</td>
                    <td>0</td>

                    <td>{{ formatPrice( ($item['working_days_in_month']/$sum_work) * $item['salary'], true) }}</td>
                </tr>
            @empty
            @endforelse

        </tbody>
    </table>


    <table class="footer-table">
        <thead>
            <tr>
                <th>Ngày công</th>
                <th>Tổng lương</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $monthlyWorkday->workdays }}</td>
                <td>{{ formatPrice($monthlyWorkday->salary, true) }}</td>
            </tr>

        </tbody>
    </table>

</body>

</html>
