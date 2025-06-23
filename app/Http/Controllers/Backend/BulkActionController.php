<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BulkActionController extends Controller
{
    public function handleBulkAction(Request $request)
    {
        $type = request()->input('type');

        $credentials = $request->validate([
            'model' => 'required|string',
            'ids' => 'required',
        ], __('request.messages'), [
            'model' => 'Tên model',
            'ids' => 'Danh sách ID',
        ]);

        $customModels = [
            'Permission' => \Spatie\Permission\Models\Permission::class,
            'Role'       => \Spatie\Permission\Models\Role::class,
        ];

        $modelClass = $customModels[$credentials['model']] ?? "App\\Models\\" . $credentials['model'];

        // Kiểm tra xem class có tồn tại hay không
        if (!class_exists($modelClass)) {
            return response()->json(['message' => 'Model không hợp lệ.'], 400);
        }

        if (!is_array($credentials['ids']))
            $credentials['ids'] = [$credentials['ids']];

        try {
            switch ($type) {
                case 'delete':
                    $modelClass::whereIn('id', $credentials['ids'])->delete();
                    return response()->json(['message' => 'Xóa thành công!'], 200);

                case 'change-status':
                    $modelClass::whereIn('id', $credentials['ids'])->get()->map(function ($q) {
                        return $q->update(['status' => $q->status = !$q->status]);
                    });
                    return response()->json(['success' => true, 'message' => 'Thay đổi trạng thái thành công!'], 200);
                default:
                    return response()->json(['success' => false, 'message' => 'Loại hành động không hợp lệ.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }
}
