<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EducationLevel;
use App\Models\Position;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {


        $maxRows = max([
            Position::count(),
            Department::count(),
            EducationLevel::count(),
        ]);

        $rows = [];

        for ($i = 1; $i <= $maxRows; $i++) {
            $rows[] = [
                'positions' => Position::find($i),
                'departments' => Department::find($i),
                'educations' => EducationLevel::find($i),
            ];
        }

        return view('backend.config.category', compact('rows'));
    }

    public function updateOrCreateOrDelete(Request $request)
    {
        $request->validate([
            'model' => 'required|string',
            'field' => 'required|string',
            'value' => 'nullable|string|max:255',
            'id' => 'nullable|integer',
        ]);

        // Map tên model với class thật
        $models = [
            'Position' => Position::class,
            'Department' => Department::class,
            'EducationLevel' => EducationLevel::class,
            // Add thêm nếu cần
        ];

        $modelName = $request->model;
        $field = $request->field;
        $value = $request->value;
        $id = $request->id;

        if (!array_key_exists($modelName, $models)) {
            return response()->json(['message' => 'Model không hợp lệ.'], 400);
        }

        $modelClass = $models[$modelName];

        if ($id) {
            // Có ID ⇒ cập nhật hoặc xoá nếu rỗng
            $item = $modelClass::find($id);

            if (!$item) {
                return response()->json(['message' => 'Không tìm thấy bản ghi.'], 404);
            }

            if ($value === null || $value === '') {
                $item->delete();
                return response()->json(['message' => "{$modelName} đã xoá", 'deleted' => true]);
            }

            $item->{$field} = $value;
            $item->save();

            return response()->json(['message' => "{$modelName} đã cập nhật", 'id' => $item->id]);
        } else {
            // Không có ID ⇒ tạo mới nếu có nội dung
            if ($value === null || $value === '') {
                return response()->json(['message' => 'Không thể tạo mới với giá trị rỗng.'], 400);
            }

            $item = $modelClass::create([
                $field => $value
            ]);

            return response()->json(['message' => "{$modelName} đã thêm", 'id' => $item->id]);
        }

    }
}
