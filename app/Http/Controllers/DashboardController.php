<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $selectedMonth = $request->input('month', date('Y-m'));
        $month = Carbon::parse($selectedMonth);
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();
        
        // Total employees
        $totalEmployees = Employee::count();
        
        // Calculate total absents for the selected month
        $totalAbsents = $this->calculateAbsents($startDate, $endDate);
        
        // Get employees with attendance data and grades
        $employees = Employee::with(['attendances' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('occurred_at', [$startDate, $endDate]);
        }])->get()->map(function ($employee) use ($startDate, $endDate) {
            $attendanceStats = $this->calculateAttendanceStats($employee, $startDate, $endDate);
            $grade = $this->calculateGrade($attendanceStats);
            
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'employee_id' => $employee->employee_id,
                'salary' => $employee->salary,
                'total_days' => $attendanceStats['total_days'],
                'present_days' => $attendanceStats['present_days'],
                'absent_days' => $attendanceStats['absent_days'],
                'late_days' => $attendanceStats['late_days'],
                'attendance_rate' => $attendanceStats['attendance_rate'],
                'grade' => $grade,
            ];
        });
        
        return view('dashboard', compact('totalEmployees', 'totalAbsents', 'employees', 'selectedMonth'));
    }
    
    private function calculateAbsents(Carbon $startDate, Carbon $endDate): int
    {
        // Get all working days in the month (excluding weekends)
        $workingDays = 0;
        $absentDays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($dayOfWeek != Carbon::SATURDAY && $dayOfWeek != Carbon::SUNDAY) {
                $workingDays++;
                $dateKey = $currentDate->format('Y-m-d');
                
                // Get all employees
                $employees = Employee::pluck('id');
                
                // Check if any employee has both checkin and checkout for this day
                foreach ($employees as $employeeId) {
                    $checkin = Attendance::where('employee_id', $employeeId)
                        ->where('status', 'checkin')
                        ->whereDate('occurred_at', $dateKey)
                        ->exists();
                    
                    $checkout = Attendance::where('employee_id', $employeeId)
                        ->where('status', 'checkout')
                        ->whereDate('occurred_at', $dateKey)
                        ->exists();
                    
                    // If both checkin and checkout are missing, it's an absent
                    if (!$checkin && !$checkout) {
                        $absentDays++;
                    }
                }
            }
            $currentDate->addDay();
        }
        
        return $absentDays;
    }
    
    private function calculateAttendanceStats(Employee $employee, Carbon $startDate, Carbon $endDate): array
    {
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->get();
        
        // Calculate working days
        $workingDays = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($dayOfWeek != Carbon::SATURDAY && $dayOfWeek != Carbon::SUNDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }
        
        // Group attendances by date
        $attendanceByDate = [];
        foreach ($attendances as $attendance) {
            $date = Carbon::parse($attendance->occurred_at)->format('Y-m-d');
            if (!isset($attendanceByDate[$date])) {
                $attendanceByDate[$date] = [
                    'checkin' => false,
                    'checkout' => false,
                    'late' => false,
                ];
            }
            
            if ($attendance->status === 'checkin') {
                $attendanceByDate[$date]['checkin'] = true;
                // Check if late (after 10:00 AM)
                $checkinTime = Carbon::parse($attendance->occurred_at);
                if ($checkinTime->format('H:i') > '10:00') {
                    $attendanceByDate[$date]['late'] = true;
                }
            } elseif ($attendance->status === 'checkout') {
                $attendanceByDate[$date]['checkout'] = true;
            }
        }
        
        // Calculate stats
        $presentDays = 0;
        $absentDays = 0;
        $lateDays = 0;
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($dayOfWeek != Carbon::SATURDAY && $dayOfWeek != Carbon::SUNDAY) {
                $dateKey = $currentDate->format('Y-m-d');
                $dayAttendance = $attendanceByDate[$dateKey] ?? ['checkin' => false, 'checkout' => false, 'late' => false];
                
                if ($dayAttendance['checkin'] || $dayAttendance['checkout']) {
                    $presentDays++;
                    if ($dayAttendance['late']) {
                        $lateDays++;
                    }
                } else {
                    $absentDays++;
                }
            }
            $currentDate->addDay();
        }
        
        $attendanceRate = $workingDays > 0 ? ($presentDays / $workingDays) * 100 : 0;
        $lateRate = $presentDays > 0 ? ($lateDays / $presentDays) * 100 : 0;
        
        return [
            'total_days' => $workingDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'attendance_rate' => round($attendanceRate, 1),
            'late_rate' => round($lateRate, 1),
        ];
    }
    
    private function calculateGrade(array $stats): string
    {
        $attendanceRate = $stats['attendance_rate'];
        
        // Good: 90% or above attendance rate
        if ($attendanceRate >= 90) {
            return 'Good';
        }
        
        // Average: 75% to 89% attendance rate
        if ($attendanceRate >= 75 && $attendanceRate < 90) {
            return 'Average';
        }
        
        // Bad: Below 75% attendance rate
        return 'Bad';
    }
}
