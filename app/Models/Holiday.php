<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'holiday_date', // Keep for backward compatibility
        'start_date',
        'end_date',
        'description',
        'is_recurring',
        'year',
    ];

    protected function casts(): array
    {
        return [
            'holiday_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_recurring' => 'boolean',
        ];
    }

    /**
     * Check if a given date falls within this holiday's date range
     */
    public function includesDate(Carbon $date): bool
    {
        if ($this->is_recurring) {
            // For recurring holidays, create the holiday dates in the current year
            $currentYear = $date->year;
            $recurringStart = Carbon::create($currentYear, $this->start_date->month, $this->start_date->day);
            $recurringEnd = Carbon::create($currentYear, $this->end_date->month, $this->end_date->day);
            
            // Handle year-spanning holidays (e.g., Dec 25 - Jan 1)
            if ($recurringStart->month > $recurringEnd->month || 
                ($recurringStart->month == $recurringEnd->month && $recurringStart->day > $recurringEnd->day)) {
                // Holiday spans across year boundary - check if date is after start or before end
                return $date->month == $recurringStart->month && $date->day >= $recurringStart->day ||
                       $date->month == $recurringEnd->month && $date->day <= $recurringEnd->day ||
                       ($date->month > $recurringStart->month && $date->month <= 12) ||
                       ($date->month >= 1 && $date->month < $recurringEnd->month);
            } else {
                // Normal case: same month or sequential months
                return $date->between($recurringStart, $recurringEnd);
            }
        } else {
            // For one-time holidays, check if date is within the range
            return $date->between($this->start_date, $this->end_date);
        }
    }

    /**
     * Get the number of days in this holiday
     */
    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}
