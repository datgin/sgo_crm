<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'position_id',
        'department_id',
        'education_level_id',
        'employment_status_id',
        'code',
        'full_name',
        'avatar',
        'phone',
        'address',
        'birthday',
        'gender',
        'cccd',
        'cccd_issued_date',
        'university_start_date',
        'university_end_date',
        'resignation_date',
        'notes',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
}
