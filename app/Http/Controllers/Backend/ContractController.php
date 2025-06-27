<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    //
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $sub = Contract::select('employee_id', DB::raw('MAX(start_date) as latest_start'))
            ->groupBy('employee_id');

        $latestContracts = Contract::query()
            ->when(!auth('admin')->user()->isAdmin(), function ($q) {
                $q->where('contracts.employee_id', auth('admin')->id());
            })
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('contracts.employee_id', '=', 'latest.employee_id')
                    ->on('contracts.start_date', '=', 'latest.latest_start');
            })
            ->get();


            $mapped = $latestContracts->map(function ($item, $index) {
                return [
                    'stt' => $index + 1,
                    'code' => '<a href="' . route('contracts.show', $item->employee_id) . '" >' . e($item->employee->code) . '</a>',
                    'full_name' => $item->employee->full_name,
                    'position' => $item->employee->position->name,
                    'department' => $item->employee->department->name,
                    'contract_type' =>  $item->contractType->name,
                    'file' => $item->file_url
                        ? '<a href="' . asset($item->file_url) . '" target="_blank">Xem file</a>'
                        : '<span class="text-muted">Chưa có</span>',
                    'date' => $item->start_date->format('d/m/Y') . ' - ' . $item->end_date->format('d/m/Y'),

                ];
            });

            return response()->json([
                'data' => $mapped,
            ]);
        }

        return view('backend.contract.index');
    }

    public function show($id)
    {
        $contracts = Contract::where('employee_id', $id)
            ->orderBy('end_date', 'desc')
            ->get();


        if ($contracts->isEmpty()) {
            abort(404);
        }
        $employee = Employee::find($id);
        return view('backend.contract.show', compact('contracts', 'employee'));
    }
}
