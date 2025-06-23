<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInOutController extends Controller
{
    public function checkIn(Request $request)
    {
        $user_id =  21; // Hoặc $request->user()

        $request->validate([
            'check_in_time' => 'required|string',
        ]);

        AttendanceRecord::updateOrCreate(
            [
                'employee_id' => $user_id,
                'date' => now()->toDateString(),
            ],
            [
                'check_in' => now()->format('H:i:s'),
                'status' => 'present',
                'created_by' => $user_id
            ]
        );

        return response()->json(['message' => 'Check in thành công']);
    }
}
