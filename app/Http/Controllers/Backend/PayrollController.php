<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayrollController extends Controller
{
    public function calculateMonthlyPayroll(Request $request)
    {


        $month = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        $employees = Employee::get();
        // dd($employees);
        $results = [];

        foreach ($employees as $employee) {
            $contracts = Contract::where('employee_id', $employee->id)
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                        ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                                $q->where('start_date', '<', $startOfMonth)
                                    ->where(function ($subQ) use ($endOfMonth, $startOfMonth) {
                                                    $subQ->where('end_date', '>=', $startOfMonth)
                                                        ->orWhereNull('end_date');
                                                });
                            });
                })
                ->orderBy('start_date')
                ->get();

            $totalSalary = 0;

            $today = now()->min($endOfMonth);

            foreach ($contracts as $contract) {
                $contractStart = Carbon::parse($contract->start_date)->max($startOfMonth);
                $contractEnd = $contract->end_date
                    ? Carbon::parse($contract->end_date)->min($today)
                    : $today;

                if ($contractEnd < $contractStart) {
                    continue;
                }


                $daysWorked = 0;
                $date = $contractStart->copy();
                while ($date->lte($contractEnd)) {
                    if ($date->dayOfWeek !== Carbon::SUNDAY) {
                        $daysWorked++;
                    }
                    $date->addDay();
                }

                $dailyRate = $contract->salary / $daysInMonth;
                $totalSalary += $daysWorked * $dailyRate;
            }



            $results[] = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->full_name,
                'month' => $month,
                'salary' => round($totalSalary),
            ];
        }

        return response()->json($results);
    }

    public function calculatePayrollWithCustomWorkdays(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();


        $workdaysInput = 0;
        $date = $startOfMonth->copy();
        while ($date->lte($endOfMonth)) {
            if ($date->dayOfWeek !== Carbon::SUNDAY) {
                $workdaysInput++;
            }
            $date->addDay();
        }

        $standardWorkdays = $workdaysInput;
 
        $employees = Employee::all();
        $results = [];

        foreach ($employees as $employee) {
            $contract = Contract::where('employee_id', $employee->id)
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->where('start_date', '<=', $endOfMonth)
                        ->where(function ($q2) use ($startOfMonth) {
                                $q2->where('end_date', '>=', $startOfMonth)
                                    ->orWhereNull('end_date');
                            });
                })
                ->orderByDesc('start_date')
                ->first();

            if (!$contract) {
                $results[] = [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->full_name,
                    'month' => $month,
                    'workdays' => 0,
                    'salary' => 0,
                    'note' => 'Không có hợp đồng',
                ];
                continue;
            }

            $dailyRate = $contract->salary / $standardWorkdays;
            $totalSalary = $dailyRate * 20;

            $results[] = [
                'employee_id' => $employee->id,
                'employee_name' => $employee->full_name,
                'month' => $month,
                'workdays' => 20,
                'salary' => round($totalSalary),
            ];
        }

        return response()->json($results);
    }



}
