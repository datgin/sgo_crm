<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\MonthlyWorkday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonthlyWorkdayController extends Controller
{

    public function index(Request $request)
    {
        $this->checkAndGenerate();
        $today = Carbon::today();
        $month = $request->get('month', $today->format('Y-m'));

        if ($request->ajax()) {
            $records = MonthlyWorkday::with(['employee.position', 'employee.department'])
                ->where('month', $month)
                ->get();

            $data = $records->map(function ($record, $index) {
                $employee = $record->employee;

                return [
                    'stt' => $index + 1,
                    'code' => $employee->code,
                    'full_name' => $employee->full_name,
                    'position' => $employee->position->name ?? '',
                    'department' => $employee->department->name ?? '',
                    'workdays' => '<input type="number" class="form-control form-control-sm workday-input"
                         data-id="' . $record->id . '" value="' . ($record->workdays ?? 0) . '" min="0" max="31">',
                    'salary' => number_format($record->salary ?? 0) . ' VNĐ',
                ];
            });

            return response()->json(['data' => $data]);
        }

        return view('backend.monthly-workdays.index', compact('month'));
    }

    public function updateWorkdays(Request $request, $id)
    {
        // dd($request->toArray());
        try {
            $record = MonthlyWorkday::findOrFail($id);
            $record->workdays = (int) $request->input('workdays');

            $start = Carbon::parse($record->month . '-01')->startOfMonth();
            $end = $start->copy()->endOfMonth();


            $workingDays = [];
            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                if (!$date->isSunday()) {
                    $workingDays[] = $date->copy();
                }
            }

            $sum = count($workingDays);


            $request->validate([
                'workdays' => 'required|numeric|min:0|max:' . $sum
            ], [
                    'workdays.required' => 'Vui lòng nhập số ngày công.',
                    'workdays.numeric' => 'Số ngày công phải là một số.',
                    'workdays.min' => 'Số ngày công phải lớn hơn hoặc bằng 0.',
                    'workdays.max' => 'Số ngày công không được lớn hơn ' . $sum . '.',
                ]);


            if ($record->employee && $record->employee->contracts()->exists()) {
                $contracts = $record->employee->contracts()
                    ->orderBy('start_date', 'asc')
                    ->get();

                $salary = 0;
                $actualWorkdays = $record->workdays;
                $countedDays = 0;

                foreach ($workingDays as $date) {
                    if ($countedDays >= $actualWorkdays)
                        break;


                    $contract = $contracts->first(function ($c) use ($date) {
                        $startDate = Carbon::parse($c->start_date);
                        $endDate = $c->end_date ? Carbon::parse($c->end_date) : null;

                        return $date->between($startDate, $endDate ?? Carbon::maxValue());
                    });

                    if ($contract) {
                        $daily = $contract->salary / $sum;
                        $salary += $daily;
                        $countedDays++;
                    }
                }

                $record->salary = round($salary);
            } else {
                $record->salary = 0;
            }

            $record->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error("Cập nhật ngày công lỗi: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật dữ liệu.',
            ], 500);
        }
    }



    public function checkAndGenerate()
    {
        $month = now()->format('Y-m');
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();


        $validEmployees = Employee::where('employment_status_id', 2)
            ->whereHas('contracts', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where(function ($contractQ) use ($startOfMonth, $endOfMonth) {
                    $contractQ->where('start_date', '<=', $endOfMonth)
                        ->where(function ($dateQ) use ($startOfMonth) {
                                $dateQ->where('end_date', '>=', $startOfMonth)
                                    ->orWhereNull('end_date');
                            });
                });
            })
            ->get();

        $validEmployeeIds = $validEmployees->pluck('id')->toArray();


        $deleted = MonthlyWorkday::where('month', $month)
            ->whereNotIn('employee_id', $validEmployeeIds)
            ->delete();


        $created = 0;
        foreach ($validEmployees as $employee) {
            $alreadyExists = MonthlyWorkday::where('employee_id', $employee->id)
                ->where('month', $month)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            MonthlyWorkday::create([
                'employee_id' => $employee->id,
                'month' => $month,
                'workdays' => null,
                'salary' => null,
            ]);

            $created++;
        }

        Log::info("Tạo $created bản ghi và xóa $deleted bản ghi MonthlyWorkday cho tháng $month");
    }


}
