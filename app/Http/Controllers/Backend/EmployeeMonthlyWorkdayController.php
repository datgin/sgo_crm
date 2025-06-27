<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\MonthlyWorkday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeMonthlyWorkdayController extends Controller
{
    //

    public function index(Request $request)
    {
        $employee = Employee::where('email', Auth::user()->email)->first();
        $month = $request->get('month', now()->format('Y-m'));

        $monthlyWorkday = MonthlyWorkday::with(['employee.position', 'employee.department'])
            ->where('month', $month)
            ->where('employee_id', $employee->id)
            ->first();

        // Nếu là AJAX thì trả về HTML để render lại tbody
        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.monthly-workdays.table_row', compact('monthlyWorkday'))->render()
            ]);
        }

        // Nếu là request thường thì trả về view đầy đủ
        return view('backend.monthly-workdays.employee', compact('month', 'monthlyWorkday'));
    }
}
