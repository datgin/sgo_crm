<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BirthDayController extends Controller
{


    public function index(Request $request)
    {
        $today = Carbon::today();
        $month = $request->get('month', $today->month);

        Log::info($month);
        Log::info($month);
        $employees = Employee::with(['position', 'department'])
            ->select('id', 'code', 'full_name', 'position_id', 'department_id', 'gender', 'birthday')
            ->whereMonth('birthday', $month)
            ->get();

        $totalInMonth = $employees->count();

        $todayInSelectedMonth = Carbon::create($today->year, $month, $today->day);

        $totalToday = $employees->filter(function ($emp) use ($todayInSelectedMonth) {
            $birthDate = Carbon::parse($emp->birthday);
            return $birthDate->day == $todayInSelectedMonth->day
                && $birthDate->month == $todayInSelectedMonth->month;
        })->count();


        $mapped = $employees->map(function ($emp) use ($today, $month) {
            $birthDate = Carbon::parse($emp->birthday);

            $todayInSelectedMonth = Carbon::create($today->year, $month, $today->day);

            $thisYearBirthday = $birthDate->copy()->year($today->year);

            if ((int)$month < $today->month) {
                $daysLeft = 0;
            } else {

                $daysLeft = $thisYearBirthday->greaterThanOrEqualTo($todayInSelectedMonth)
                    ? $thisYearBirthday->diffInDays($todayInSelectedMonth)
                    : 0;
            }

            return [
                'code' => $emp->code,
                'full_name' => $emp->full_name,
                'position' => $emp->position->name ?? '',
                'department' => $emp->department->name ?? '',
                'gender' => match ($emp->gender) {
                    'male' => 'Nam',
                    'female' => 'Nữ',
                    'other' => 'Khác',
                    default => 'Không rõ',
                },
                'birthday' => $birthDate->format('d/m/Y'),
                'birthday_this_year' => $thisYearBirthday->format('d/m/Y'),
                'days_left' => $daysLeft,
            ];
        });






        if ($request->ajax()) {
            Log::info($request->all());
            return response()->json([
                'data' => $mapped,
                'total_in_month' => $totalInMonth,
                'total_today' => $totalToday,
                'today' => $today->format('d/m/Y'),
            ]);
        }

        return view('backend.birthday.index', [
            'total_in_month' => $totalInMonth,
            'total_today' => $totalToday,
            'today' => $today->format('d/m/Y'),
        ]);
    }



}
