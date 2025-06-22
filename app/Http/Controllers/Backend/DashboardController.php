<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $statistics = $this->getGeneralStatistics();
        [$departments, $series] = $this->getEmployeeStatusByDepartment();
        $departmentPercentage = $this->getDepartmentEmployeePercentage();
        $genderStructure = $this->getGenderStructure();
        $educationStructure = $this->getEducationLevelStructure();
        $contractStructure = $this->getActiveEmployeeContractStructure();
        [$seniorityDepartments, $senioritySeries] = $this->getSeniorityByDepartment();

        return view('backend.dashboard', compact(
            'statistics',
            'departments',
            'series',
            'departmentPercentage',
            'genderStructure',
            'educationStructure',
            'contractStructure',
            'seniorityDepartments',
            'senioritySeries'
        ));
    }

    /**
     * Lấy thống kê tổng quan nhân sự.
     */
    private function getGeneralStatistics()
    {
        return DB::table('employees')
            ->selectRaw('
                COUNT(*) as total_employees,
                SUM(CASE WHEN employment_status_id = 1 AND status = 1 THEN 1 ELSE 0 END) as active_employees,
                SUM(CASE WHEN employment_status_id = 2 THEN 1 ELSE 0 END) as resigned_employees,
                SUM(CASE WHEN MONTH(birthday) = ? THEN 1 ELSE 0 END) as birthday_this_month
            ', [Carbon::now()->month])
            ->first();
    }

    /**
     * Lấy thống kê trạng thái nhân viên theo phòng ban (cho Highcharts).
     */
    private function getEmployeeStatusByDepartment(): array
    {
        $data = DB::table('employees as e')
            ->join('departments as d', 'e.department_id', '=', 'd.id')
            ->join('employment_statuses as s', 'e.employment_status_id', '=', 's.id')
            ->select('d.id as dept_id', 'd.name as department', 's.id as status_id', 's.name as status', DB::raw('COUNT(*) as total'))
            ->groupBy('d.id', 'd.name', 's.id', 's.name') // group theo ID để đảm bảo an toàn
            ->get();

        $departments = [];
        $statuses = [];

        foreach ($data as $row) {
            // tránh trùng phòng ban
            if (!in_array($row->department, $departments)) {
                $departments[] = $row->department;
            }

            $statuses[$row->status][$row->department] = $row->total;
        }

        // ⚠️ Reset key tuần tự để highcharts hoạt động đúng
        $departments = array_values($departments);

        $series = [];
        foreach ($statuses as $statusName => $deptData) {
            $series[] = [
                'name' => $statusName,
                'data' => array_map(function ($dept) use ($deptData) {
                    return $deptData[$dept] ?? 0;
                }, $departments),
            ];
        }

        return [$departments, $series];
    }

    private function getDepartmentEmployeePercentage()
    {
        $data = DB::table('employees as e')
            ->join('departments as d', 'e.department_id', '=', 'd.id')
            ->select('d.name as department', DB::raw('COUNT(*) as total'))
            ->groupBy('d.name')
            ->get();

        $totalEmployees = $data->sum('total');

        $chartData = $data->map(function ($item) use ($totalEmployees) {
            return [
                'name' => $item->department,
                'y' => round(($item->total / $totalEmployees) * 100, 2),
            ];
        });

        return $chartData->toArray();
    }

    private function getGenderStructure()
    {
        $raw = DB::table('employees')
            ->select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender')
            ->toArray();

        // Gắn nhãn tiếng Việt
        $labels = [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];

        $result = [];
        foreach ($labels as $key => $label) {
            $result[] = [
                'name' => $label,
                'y' => $raw[$key] ?? 0,
            ];
        }

        return $result;
    }

    private function getEducationLevelStructure()
    {
        return DB::table('employees as e')
            ->join('education_levels as el', 'e.education_level_id', '=', 'el.id')
            ->select('el.name as level', DB::raw('COUNT(*) as total'))
            ->groupBy('el.name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->level,
                    'y' => $item->total,
                ];
            })
            ->toArray();
    }

    private function getActiveEmployeeContractStructure()
    {
        return DB::table('contracts as c')
            ->join('contract_types as ct', 'c.contract_type_id', '=', 'ct.id')
            ->join('employees as e', 'c.employee_id', '=', 'e.id')
            // ->where('e.employment_status_id', 1)
            ->where('e.status', 1) // Active
            ->select('ct.name as contract_type', DB::raw('COUNT(*) as total'))
            ->groupBy('ct.name')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->contract_type,
                    'y' => $item->total,
                ];
            })
            ->toArray();
    }

    private function getSeniorityByDepartment()
    {
        // Subquery: lấy hợp đồng mới nhất của mỗi nhân viên
        $latestContracts = DB::table('contracts')
            ->select('employee_id', DB::raw('MIN(start_date) as join_date'))
            ->groupBy('employee_id');

        // Join với employees, departments, và subquery hợp đồng
        $data = DB::table('employees as e')
            ->joinSub($latestContracts, 'lc', function ($join) {
                $join->on('e.id', '=', 'lc.employee_id');
            })
            ->join('departments as d', 'e.department_id', '=', 'd.id')
            // ->where('e.employment_status_id', 1)
            ->where('e.status', 1)
            ->selectRaw("
                d.name as department,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, lc.join_date, CURDATE()) < 1 THEN 1 ELSE 0 END) as under_1_year,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, lc.join_date, CURDATE()) BETWEEN 1 AND 3 THEN 1 ELSE 0 END) as from_1_to_3_years,
                SUM(CASE WHEN TIMESTAMPDIFF(YEAR, lc.join_date, CURDATE()) > 3 THEN 1 ELSE 0 END) as over_3_years
            ")
            ->groupBy('d.name')
            ->get();

        // Tách dữ liệu cho Highcharts
        $departments = $data->pluck('department')->toArray();

        $under1 = $data->pluck('under_1_year')->map(fn($x) => (int) $x)->toArray();
        $from1to3 = $data->pluck('from_1_to_3_years')->map(fn($x) => (int) $x)->toArray();
        $over3 = $data->pluck('over_3_years')->map(fn($x) => (int) $x)->toArray();


        $series = [
            ['name' => '< 1 năm', 'data' => $under1],
            ['name' => '1 - 3 năm', 'data' => $from1to3],
            ['name' => '> 3 năm', 'data' => $over3],
        ];

        return [$departments, $series];
    }
}
