<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\Employee\EmployeeCollection;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $employees = Employee::select('id', 'code', 'full_name', 'phone')
                ->orderBy('id', 'desc')
                ->paginate(1);

            $response = new EmployeeCollection($employees);

            return response()->json($response);
        }

        return view('backend.employee.index');
    }
}

