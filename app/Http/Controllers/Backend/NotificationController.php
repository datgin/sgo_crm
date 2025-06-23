<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('backend.notification.index');
    }

    public function send(Request $request)
    {
        $employee = Employee::findOrFail($request->employeeId);

        $user = User::query()->where('employee_id', $employee->id)->firstOrFail();

        $now = now();
        $thang = $now->format('m');
        $nam = $now->format('Y');

        $user->notify(new UserNotification(
            'Tổng kết bảng lương', // message chính (nên dùng làm title)
            [
                'body' => "Bảng lương tháng {$thang}/{$nam} của bạn đã được cập nhật. Vui lòng kiểm tra tại mục Bảng lương.",
            ]
        ));


        return response()->json(['message' => "Gửi thông báo thành công."]);
    }
}
