<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermisstionRequest;
use App\Traits\DataTables;
use App\Traits\QueryBuilder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use QueryBuilder;
    use DataTables;

    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = $this->queryBuilder(
                model: new Permission,
            );

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('operations', fn($row) => view('components.operation', ['row' => $row])->render()),

            );
        }

        $groupNames = $this->getUniqueGroupNames();

        return view('backend.permission.index', compact('groupNames'));
    }

    private function getUniqueGroupNames()
    {
        return Permission::query()
            ->select('group_name')
            ->whereNotNull('group_name')
            ->distinct()
            ->orderBy('group_name', 'asc')
            ->pluck('group_name')
            ->values(); // Đảm bảo trả về collection chỉ gồm giá trị
    }

    public function store(PermisstionRequest $request)
    {
        $credentials = $request->validated();

        $credentials['name'] = ucwords(mb_strtolower($credentials['name']));

        $credentials['vi_name'] = ucfirst(mb_strtolower($credentials['vi_name']));

        $permission =  Permission::create($credentials);

        return successResponse("Tạo mới quyền thành công", $permission, 201, true, false);
    }

    public function show(string $id)
    {
        $permission = Permission::findOrFail($id);

        return successResponse("Lấy dữ liệu thành công.", $permission, 200, true, false);
    }

    public function update(PermisstionRequest $request)
    {
        $credentials = $request->validated();

        $permission = Permission::query()->findOrFail($credentials['id']);

        $credentials['name'] = ucwords(mb_strtolower($credentials['name']));

        $credentials['vi_name'] = ucfirst(mb_strtolower($credentials['vi_name']));

        $permission->update($credentials);

        return successResponse("Cập nhật quyền thành công", $permission, 200, true, false);
    }
}
