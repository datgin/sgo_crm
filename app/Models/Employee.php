<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
        'notes'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    protected $casts = [
        'birthday' => 'date',
        'cccd_issued_date' => 'date',
        'university_start_date' => 'date',
        'university_end_date' => 'date',
        'resignation_date' => 'date',
    ];

    public function getNameCodeAttribute()
    {
        return "$this->full_name - $this->code";
    }

    public function getAgeAttribute()
    {
        if (!$this->birthday) {
            return null;
        }

        return Carbon::now()->diffInYears(Carbon::parse($this->birthday));
    }

    public function getDaysLeftForUniversityAttribute(): ?string
    {
        if (!$this->university_end_date) {
            return null; // hoặc trả "Chưa cập nhật"
        }

        $endDate = Carbon::parse($this->university_end_date);
        $now = Carbon::now();

        if ($endDate->isPast()) {
            return "Đã kết thúc";
        }

        return $now->diffInDays($endDate) . ' ngày';
    }

    public function getSeniorityAttribute(): string
    {
        if (!$this->university_start_date) {
            return 'Chưa xác định';
        }

        $start = Carbon::parse($this->university_start_date);
        $end = $this->resignation_date ? Carbon::parse($this->resignation_date) : now();
        $years = $start->diffInYears($end);

        return match (true) {
            $years < 1     => '< 1 năm',
            $years <= 3    => '1 - 3 năm',
            default        => '> 3 năm',
        };
    }

    public function getAvatarAttribute($avatar): string
    {
        return showImage($avatar);
    }
}
