<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use App\Models\Department;
use App\Models\EducationLevel;
use App\Models\EmploymentStatus;
use App\Models\Position;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // public function index()
    // {
    //     $positions = Position::all()->values();
    //     $departments = Department::all()->values();
    //     $educations = EducationLevel::all()->values();

    //     $maxRows = max([
    //         $positions->count(),
    //         $departments->count(),
    //         $educations->count(),
    //     ]);

    //     // Pad từng mảng cho đủ số hàng
    //     $positions = $positions->pad($maxRows, null);
    //     $departments = $departments->pad($maxRows, null);
    //     $educations = $educations->pad($maxRows, null);

    //     $rows = [];

    //     for ($i = 0; $i < $maxRows; $i++) {
    //         $rows[] = [
    //             'positions' => $positions[$i],
    //             'departments' => $departments[$i],
    //             'educations' => $educations[$i],
    //         ];
    //     }

    //     return view('backend.config.category', compact('rows'));
    // }

    public function index()
    {
        $departments = Department::pluck('name')->toArray();
        $positions = Position::pluck('name')->toArray();
        $education_levels = EducationLevel::pluck('name')->toArray();
        $contract_types = ContractType::pluck('name')->toArray();
        $employment_statuses = EmploymentStatus::pluck('name')->toArray();

        $maxRows = max(
            count($departments),
            count($positions),
            count($education_levels),
            count($contract_types),
            count($employment_statuses)
        );

        return view('backend.config.category', compact(
            'departments',
            'positions',
            'education_levels',
            'contract_types',
            'employment_statuses',
            'maxRows'
        ));
    }

    public function updateOrCreate(Request $request)
    {
        $request->validate([
            'table' => 'required|in:departments,positions,education_levels,contract_types,employment_statuses',
            'name' => 'required|string|max:255',
        ]);

        $modelMap = $this->modelMap();
        $model = $modelMap[$request->table];

        // Kiểm tra xem đã tồn tại chưa
        $record = $model::where('name', $request->name)->first();

        if ($record) {
            return errorResponse("Tên \"{$request->name}\" đã tồn tại !", 422, true);
        }

        $newRecord = $model::create([
            'name' => $request->name
        ]);

        return successResponse("Thêm dữ liệu thành công.", $newRecord, 200, true, false);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'table' => 'required|in:departments,positions,education_levels,contract_types,employment_statuses',
            'name' => 'required|string|max:255',
        ]);

        $modelMap = $this->modelMap();

        $model = $modelMap[$request->table];

        $record = $model::where('name', $request->name)->first();

        if ($record) {
            $record->delete();
            return successResponse("Xóa thành công.", $record, 200, true, false);
        }

        return errorResponse("Xóa bản ghi không thành công!", 404);
    }

    private function modelMap(): array
    {
        return [
            'departments' => Department::class,
            'positions' => Position::class,
            'education_levels' => EducationLevel::class,
            'contract_types' => ContractType::class,
            'employment_statuses' => EmploymentStatus::class,
        ];
    }
}
