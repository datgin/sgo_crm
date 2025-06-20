<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'contract_type_id',
        'code',
        'file_url',
        'salary',
        'start_date',
        'end_date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];
}
