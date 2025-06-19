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

    public function create()
    {
        return view('backend.contract_type.save');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file_url' => 'required|file|mimes:pdf|max:5120',
        ]);

        $filePath = $request->file('file_url')->store('uploads/contract', 'public');

        $contractType = ContractType::create([
            'name' => $request->input('name'),
            'file_url' => 'storage/' . $filePath,
        ]);

        return response()->json([
            'message' => 'Thêm loại hợp đồng thành công!',
            'data' => $contractType,
            'redirect' => route('contactTypes.index'),
        ]);
    }

    public function edit($id)
    {
        $contractType = ContractType::find($id);
        return view('backend.contract_type.save', compact('contractType'));
    }

    public function update(Request $request, $id)
    {

        $contractType = ContractType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'file_url' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $contractType->name = $request->input('name');

        if ($request->hasFile('file_url')) {
            if ($contractType->file_url && file_exists(public_path($contractType->file_url))) {
                unlink(public_path($contractType->file_url));
            }

            $filePath = $request->file('file_url')->store('uploads/contract', 'public');
            $contractType->file_url = 'storage/' . $filePath;
        }

        $contractType->save();

        return response()->json([
            'message' => 'Cập nhật thành công!',
            'data' => $contractType,
            'redirect' => route('contactTypes.index'),
        ]);
    }

    public function destroy($id)
    {
        $contractType = ContractType::find($id);

        if (!$contractType) {
            return response()->json([
                'message' => 'Không tìm thấy loại hợp đồng.',
            ], 404);
        }

        $contractType->delete();

        return response()->json([
            'message' => 'Xóa loại hợp đồng thành công!',
        ]);
    }




}
