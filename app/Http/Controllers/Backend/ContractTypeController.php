<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ContractTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $contractTypes = ContractType::select('id', 'name', 'file_url')->get();

            $mapped = $contractTypes->map(function ($item, $index) {
                return [
                    'checkbox' => '<input type="checkbox" class="row-checkbox" value="' . $item->id . '">',
                    'stt' => $index + 1,
                    'name' => $item->name,
                    'file_url' => $item->file_url
                        ? '<a href="' . asset($item->file_url) . '" target="_blank">Xem file</a>'
                        : '<span class="text-muted">Chưa có</span>',
                    'operations' => view('components.operation', ['row' => $item])->render(),
                ];
            });

            return response()->json([
                'data' => $mapped,
            ]);
        }

        return view('backend.contract_type.index');
    }

    public function edit($id){
      $contractType = ContractType::find($id);
    }

}
