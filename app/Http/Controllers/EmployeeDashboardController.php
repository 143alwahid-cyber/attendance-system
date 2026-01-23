<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get filter parameters
        $selectedMonth = $request->input('month', date('Y-m'));
        $statusFilter = $request->input('status', 'all'); // all, present, absent, late
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortOrder = $request->input('sort', 'desc'); // asc, desc

        // Determine date range
        if ($dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom)->startOfDay();
            $endDate = Carbon::parse($dateTo)->endOfDay();
        } else {
            $month = Carbon::parse($selectedMonth);
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
        }

        // Get attendances for this employee
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->orderBy('occurred_at', $sortOrder)
            ->get();

        // Get all leaves for this employee in the date range
        $leaves = Leave::where('employee_id', $employee->id)
            ->whereBetween('leave_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function ($leave) {
                return $leave->leave_date->format('Y-m-d');
            });

        // Calculate stats (always calculate for full date range, filtering happens in view)
        $attendanceStats = $this->calculateAttendanceStats($employee, $startDate, $endDate, $attendances, $leaves);
        $grade = $this->calculateGrade($attendanceStats);

        return view('employee.dashboard', compact(
            'employee',
            'attendances',
            'attendanceStats',
            'grade',
            'selectedMonth',
            'statusFilter',
            'dateFrom',
            'dateTo',
            'sortOrder'
        ));
    }

    private function calculateAttendanceStats($employee, Carbon $startDate, Carbon $endDate, $attendances, $leaves = null): array
    {
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
                    'checkin_time' => null,
                    'checkout_time' => null,
                ];
            }

            if ($attendance->status === 'checkin') {
                $attendanceByDate[$date]['checkin'] = true;
                $checkinTime = Carbon::parse($attendance->occurred_at);
                $attendanceByDate[$date]['checkin_time'] = $checkinTime;
                // Check if late (after 10:00 AM)
                if ($checkinTime->format('H:i') > '10:00') {
                    $attendanceByDate[$date]['late'] = true;
                }
            } elseif ($attendance->status === 'checkout') {
                $attendanceByDate[$date]['checkout'] = true;
                $attendanceByDate[$date]['checkout_time'] = Carbon::parse($attendance->occurred_at);
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
                
                // Check if there's an approved leave for this date
                $approvedLeave = $leaves ? $leaves->get($dateKey) : null;
                $hasApprovedLeave = $approvedLeave && $approvedLeave->status === 'approved';
                
                if ($hasApprovedLeave) {
                    // If there's an approved leave, mark it in attendance data
                    if (!isset($attendanceByDate[$dateKey])) {
                        $attendanceByDate[$dateKey] = [
                            'checkin' => false,
                            'checkout' => false,
                            'late' => false,
                            'checkin_time' => null,
                            'checkout_time' => null,
                        ];
                    }
                    $attendanceByDate[$dateKey]['has_leave'] = true;
                    $attendanceByDate[$dateKey]['leave_type'] = $approvedLeave->leave_type;
                    $attendanceByDate[$dateKey]['leave_format'] = $approvedLeave->leave_format;
                    $attendanceByDate[$dateKey]['leave_status'] = $approvedLeave->status;
                    
                    // Approved leave counts as present (not absent)
                    $presentDays++;
                } elseif ($dayAttendance['checkin'] || $dayAttendance['checkout']) {
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

        return [
            'total_days' => $workingDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'attendance_rate' => round($attendanceRate, 1),
            'attendance_by_date' => $attendanceByDate,
        ];
    }

    private function calculateGrade(array $stats): string
    {
        $attendanceRate = $stats['attendance_rate'];

        if ($attendanceRate >= 90) {
            return 'Good';
        }

        if ($attendanceRate >= 75 && $attendanceRate < 90) {
            return 'Average';
        }

        return 'Bad';
    }
}
