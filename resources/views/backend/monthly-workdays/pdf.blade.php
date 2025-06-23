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


    <table>
        <thead>
            <tr>
                <th>Mã NV</th>
                <th>Họ tên</th>
                <th>Bộ phận</th>
                <th>Lương cơ bản</th>
                <th>Số ngày công</th>
                <th>Ngày nghỉ</th>
                <th>Tổng lương</th>
            </tr>
            {{-- <tr>
                <th>Ngày thường</th>
            </tr> --}}
        </thead>
        <tbody>
            <tr>
                <td>{{ $employee->code }}</td>
                <td>{{ $employee->full_name }}</td>
                <td>{{ $employee->department->name }}</td>
                <td>

                    @forelse ($salaryRangesForMonth as $item)
                        <div>
                            {{ formatPrice($item['salary'], true) }}
                            ({{ \Carbon\Carbon::parse($item['from'])->format('d/m/Y') }}
                            - {{ \Carbon\Carbon::parse($item['to'])->format('d/m/Y') }})
                        </div>
                    @empty
                    @endforelse


                </td>
                <td>
                    @forelse ($salaryRangesForMonth as $item)
                        <div>{{ formatPrice($item['working_days_in_month']) }}</div>
                    @empty
                    @endforelse
                </td>


                <td>{{ $sum_work - $monthlyWorkday->workdays }}</td>
                <td>{{ formatPrice($monthlyWorkday->salary, true) }}</td>
            </tr>
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
