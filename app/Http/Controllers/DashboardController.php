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

        // Load employees with attendances for the month in one go (avoids N+1)
        $employees = Employee::with(['attendances' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('occurred_at', [$startDate, $endDate]);
        }])->get();

        $totalEmployees = $employees->count();
        $totalAbsents = $this->calculateAbsents($startDate, $endDate, $employees->pluck('id'));

        // Use already-loaded attendances; no extra query per employee
        $employees = $employees->map(function ($employee) use ($startDate, $endDate) {
            $attendanceStats = $this->calculateAttendanceStats($employee, $startDate, $endDate, $employee->attendances);
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
    
    private function calculateAbsents(Carbon $startDate, Carbon $endDate, $employeeIds): int
    {
        $absentDays = 0;

        // Single query: all attendances in the date range (no per-day, per-employee queries)
        $attendances = Attendance::whereBetween('occurred_at', [$startDate, $endDate])
            ->select('employee_id', 'status', 'occurred_at')
            ->get();

        // Map: employee_id => date => ['checkin' => bool, 'checkout' => bool]
        $byEmployeeDate = [];
        foreach ($attendances as $a) {
            $d = Carbon::parse($a->occurred_at)->format('Y-m-d');
            $eid = $a->employee_id;
            if (! isset($byEmployeeDate[$eid])) {
                $byEmployeeDate[$eid] = [];
            }
            if (! isset($byEmployeeDate[$eid][$d])) {
                $byEmployeeDate[$eid][$d] = ['checkin' => false, 'checkout' => false];
            }
            if ($a->status === 'checkin') {
                $byEmployeeDate[$eid][$d]['checkin'] = true;
            }
            if ($a->status === 'checkout') {
                $byEmployeeDate[$eid][$d]['checkout'] = true;
            }
        }

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            if ($currentDate->dayOfWeek === Carbon::SATURDAY || $currentDate->dayOfWeek === Carbon::SUNDAY) {
                $currentDate->addDay();
                continue;
            }
            $dateKey = $currentDate->format('Y-m-d');
            foreach ($employeeIds as $eid) {
                $flags = $byEmployeeDate[$eid][$dateKey] ?? ['checkin' => false, 'checkout' => false];
                if (! $flags['checkin'] && ! $flags['checkout']) {
                    $absentDays++;
                }
            }
            $currentDate->addDay();
        }

        return $absentDays;
    }
    
    private function calculateAttendanceStats(Employee $employee, Carbon $startDate, Carbon $endDate, $attendancesForRange = null): array
    {
        $attendances = $attendancesForRange ?? Attendance::where('employee_id', $employee->id)
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
