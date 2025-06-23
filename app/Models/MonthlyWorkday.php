<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyWorkday extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'month',
        'workdays',
        'salary',
        'file'
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
