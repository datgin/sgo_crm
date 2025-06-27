@if ($monthlyWorkday)
    <tr>
        <td>{{ $monthlyWorkday->employee->code }}</td>
        <td>{{ $monthlyWorkday->employee->full_name }}</td>
        <td>{{ $monthlyWorkday->employee->position->name }}</td>
        <td>{{ $monthlyWorkday->employee->department->name }}</td>
        <td>{{ $monthlyWorkday->workdays }}</td>
        <td>{{ formatPrice($monthlyWorkday->salary, true) }}</td>
        <td><a href="{{ asset($monthlyWorkday->file) }}" target="_blank">Tải file</a></td>
    </tr>
@else
    <tr>
        <td colspan="7" class="text-center">Không có dữ liệu</td>
    </tr>
@endif
