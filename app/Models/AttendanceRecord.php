<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'working_hours',
        'status',
        'note',
        'created_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
