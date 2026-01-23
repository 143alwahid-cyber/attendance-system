<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_month',
        'gross_salary',
        'working_days',
        'salary_per_day',
        'salary_per_minute',
        'total_deductions',
        'late_deductions',
        'absent_deductions',
        'tax_amount',
        'overtime_minutes',
        'overtime_amount',
        'compensation',
        'net_salary',
        'daily_details',
    ];

    protected function casts(): array
    {
        return [
            'payroll_month' => 'date',
            'gross_salary' => 'decimal:2',
            'salary_per_day' => 'decimal:2',
            'salary_per_minute' => 'decimal:4',
            'total_deductions' => 'decimal:2',
            'late_deductions' => 'decimal:2',
            'absent_deductions' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'overtime_minutes' => 'decimal:2',
            'overtime_amount' => 'decimal:2',
            'compensation' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'daily_details' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
