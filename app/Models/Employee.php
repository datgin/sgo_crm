<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Model
{
    use HasFactory, HasRoles;


    protected $fillable = [
        'position_id',
        'department_id',
        'education_level_id',
        'employment_status_id',
        'code',
        'full_name',
        'email',
        'password',
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
        'status' => 'boolean'
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

        $contract = $this->contract;
        if (!$contract || !$contract->start_date) {
            return 'Chưa xác định';
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
        $contract = $this->contract;
        if (!$contract || !$contract->start_date) {
            return 'Chưa xác định';
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
        return $this->contract && $this->contract->start_date ? $this->contract->start_date->format('d-m-Y') : '<span class="text-muted">Chưa xác định</span>';
    }

    public function getEndDateAttribute()
    {
        return $this->contract && $this->contract->end_date ? $this->contract->end_date->format('d-m-Y') : '<span class="text-muted">Chưa xác định</span>';
    }
    public function getContractTypeAttribute()
    {
        return $this->contract && $this->contract->contractType ? $this->contract->contractType->name : '<span class="text-muted">Chưa xác định</span>';
    }

    protected static function booted()
    {
        static::deleting(function ($employee) {
            deleteImage($employee->avatar);
        });
    }
}
