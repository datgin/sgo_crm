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
        'email',
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
        'status'
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

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function latestContract()
    {
        return $this->hasOne(Contract::class)->latestOfMany();
    }

    protected $casts = [
        'birthday' => 'date',
        'cccd_issued_date' => 'date',
        'university_start_date' => 'date',
        'university_end_date' => 'date',
        'resignation_date' => 'date',
        'status' => 'boolean'
    ];

    public function getAgeAttribute()
    {
        if (!$this->birthday) {
            return '<small class="text-muted">Chưa cập nhật...</small>';
        }

        return Carbon::now()->diffInYears(Carbon::parse($this->birthday));
    }

    public function getSeniorityAttribute(): string
    {

        $contract = $this->latestContract;
        if (!$contract || !$contract->start_date) {
            return '<small class="text-muted">Không xác định</small>';
        }

        $start = Carbon::parse($contract->start_date);
        $end = $contract->end_date
            ? Carbon::parse($contract->end_date)
            : now();

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

    public function getGenderTextAttribute()
    {
        return match ($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            default => 'Khác',
        };
    }

    public function getSeniorityDetailAttribute(): string
    {
        $contract = $this->latestContract;
        if (!$contract || !$contract->start_date) {
            return '<small class="text-muted">Không xác định</small>';
        }

        $start = Carbon::parse($contract->start_date);
        $end   = $contract->end_date
            ? Carbon::parse($contract->end_date)
            : now();

        $diff = $start->diff($end);

        $years  = $diff->y;
        $months = $diff->m;

        $parts = [];
        if ($years > 0) {
            $parts[] = "{$years} năm";
        }
        if ($months > 0) {
            $parts[] = "{$months} tháng";
        }

        if (empty($parts)) {
            $parts[] = '< 1 tháng';
        }

        return implode(' ', $parts);
    }

    public function getStartDateAttribute()
    {
        return $this->latestContract && $this->latestContract->start_date ? $this->latestContract->start_date->format('d-m-Y') : '<span class="text-muted">Chưa xác định</span>';
    }

    public function getEndDateAttribute()
    {
        return $this->latestContract && $this->latestContract->end_date ? $this->latestContract->end_date->format('d-m-Y') : '<span class="text-muted">Chưa xác định</span>';
    }
    public function getContractTypeAttribute()
    {
        return $this->latestContract && $this->latestContract->contractType ? $this->latestContract->contractType->name : '<span class="text-muted">Chưa xác định</span>';
    }

    protected static function booted()
    {
        static::deleting(function ($employee) {
            deleteImage($employee->avatar);
        });
    }
}
