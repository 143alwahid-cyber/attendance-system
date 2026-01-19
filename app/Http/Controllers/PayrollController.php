<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;

// Try to use DomPDF facade if available
if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
    class_alias('\Barryvdh\DomPDF\Facade\Pdf', 'Pdf');
}

class PayrollController extends Controller
{
    public function index(): View
    {
        $employees = Employee::orderBy('name')->get();
        
        return view('payroll.index', compact('employees'));
    }
    
    public function generate(Request $request): View|RedirectResponse
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'month' => ['required', 'date_format:Y-m'],
            'overtime_minutes' => ['nullable', 'numeric', 'min:0'],
            'compensation' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        $employee = Employee::findOrFail($request->employee_id);
        $month = Carbon::parse($request->month);
        $overtimeMinutes = (float) ($request->input('overtime_minutes', 0));
        $compensation = (float) ($request->input('compensation', 0));
        
        $payroll = $this->calculatePayroll($employee, $month, $overtimeMinutes, $compensation);
        
        return view('payroll.show', compact('employee', 'month', 'payroll'));
    }
    
    public function exportPdf(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'month' => ['required', 'date_format:Y-m'],
            'overtime_minutes' => ['nullable', 'numeric', 'min:0'],
            'compensation' => ['nullable', 'numeric', 'min:0'],
        ]);
        
        $employee = Employee::findOrFail($request->employee_id);
        $month = Carbon::parse($request->month);
        $overtimeMinutes = (float) ($request->input('overtime_minutes', 0));
        $compensation = (float) ($request->input('compensation', 0));
        
        $payroll = $this->calculatePayroll($employee, $month, $overtimeMinutes, $compensation);
        
        // Use dompdf for backend PDF generation
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.pdf', compact('employee', 'month', 'payroll'))
                    ->setPaper('a4', 'portrait')
                    ->setOption('enable-local-file-access', true)
                    ->setOption('isRemoteEnabled', true);
                
                $fileName = 'Payroll_' . $employee->employee_id . '_' . $month->format('Y-m') . '.pdf';
                
                return $pdf->download($fileName);
            } catch (\Exception $e) {
                \Log::error('PDF generation error: ' . $e->getMessage());
                // Recalculate and show the payroll page with error
                $payroll = $this->calculatePayroll($employee, $month, $overtimeMinutes, $compensation);
                return view('payroll.show', compact('employee', 'month', 'payroll'))
                    ->with('error', 'PDF generation failed: ' . $e->getMessage());
            }
        }
        
        // Fallback: Return error if dompdf is not installed
        // Recalculate and show the payroll page with error message
        $payroll = $this->calculatePayroll($employee, $month, $overtimeMinutes, $compensation);
        return view('payroll.show', compact('employee', 'month', 'payroll'))
            ->with('error', 'PDF generation package is not installed. Please install barryvdh/laravel-dompdf package using: composer require barryvdh/laravel-dompdf');
    }
    
    private function calculatePayroll(Employee $employee, Carbon $month, float $overtimeMinutes = 0, float $compensation = 0): array
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();
        
        // Get all attendance records for the month
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('occurred_at', [$startDate, $endDate])
            ->orderBy('occurred_at')
            ->get();
        
        // Calculate salary per minute: Salary / 22 / 9 / 60
        $salaryPerMinute = $employee->salary / 22 / 9 / 60;
        $salaryPerDay = $employee->salary / 22;
        
        // Group attendances by date
        $attendanceByDate = [];
        foreach ($attendances as $attendance) {
            $date = Carbon::parse($attendance->occurred_at)->format('Y-m-d');
            if (!isset($attendanceByDate[$date])) {
                $attendanceByDate[$date] = [
                    'checkin' => null,
                    'checkout' => null,
                ];
            }
            
            if ($attendance->status === 'checkin') {
                $attendanceByDate[$date]['checkin'] = Carbon::parse($attendance->occurred_at);
            } elseif ($attendance->status === 'checkout') {
                $attendanceByDate[$date]['checkout'] = Carbon::parse($attendance->occurred_at);
            }
        }
        
        // Calculate working days (excluding weekends - Saturday=6, Sunday=0)
        $workingDays = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($dayOfWeek != Carbon::SATURDAY && $dayOfWeek != Carbon::SUNDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }
        
        // Calculate deductions
        $totalDeductions = 0;
        $lateDeductions = 0;
        $absentDeductions = 0;
        $dailyDetails = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $dateKey = $currentDate->format('Y-m-d');
            
            // Skip weekends
            if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
                $currentDate->addDay();
                continue;
            }
            
            $checkin = $attendanceByDate[$dateKey]['checkin'] ?? null;
            $checkout = $attendanceByDate[$dateKey]['checkout'] ?? null;
            
            $dayDeduction = 0;
            $isAbsent = false;
            $isLate = false;
            $lateMinutes = 0;
            
            // Check if absent (BOTH checkin AND checkout are missing)
            if (!$checkin && !$checkout) {
                $isAbsent = true;
                // Absent deduction = per day salary × 1.5
                $dayDeduction = $salaryPerDay * 1.5;
                $absentDeductions += $dayDeduction;
            } else {
                // Only check for late if checkin exists
                if ($checkin) {
                    $checkinHour = (int) $checkin->format('H');
                    $checkinMinute = (int) $checkin->format('i');
                    
                    // Check if checkin is after 10:00 AM
                    if ($checkinHour > 10 || ($checkinHour == 10 && $checkinMinute > 0)) {
                        $isLate = true;
                        
                        // Calculate late minutes: actual checkin time - 10:00 AM
                        $expectedHour = 10;
                        $expectedMinute = 0;
                        
                        // Convert to total minutes since midnight
                        $checkinTotalMinutes = ($checkinHour * 60) + $checkinMinute;
                        $expectedTotalMinutes = ($expectedHour * 60) + $expectedMinute;
                        
                        // Calculate late minutes
                        $lateMinutes = $checkinTotalMinutes - $expectedTotalMinutes;
                        
                        // Late deduction: 60 mins base + actual late minutes
                        // Example: 10:02 AM = 2 min late, so 60 + 2 = 62 minutes deduction
                        $lateDeductionMinutes = 60 + $lateMinutes;
                        $dayDeduction = $salaryPerMinute * $lateDeductionMinutes;
                        $lateDeductions += $dayDeduction;
                    }
                }
            }
            
            $totalDeductions += $dayDeduction;
            
            $dailyDetails[] = [
                'date' => $currentDate->copy(),
                'date_formatted' => $currentDate->format('M d, Y'),
                'day_name' => $currentDate->format('l'),
                'checkin' => $checkin ? $checkin->format('g:i A') : null,
                'checkout' => $checkout ? $checkout->format('g:i A') : null,
                'is_absent' => $isAbsent,
                'is_late' => $isLate,
                'late_minutes' => $lateMinutes,
                'deduction' => $dayDeduction,
            ];
            
            $currentDate->addDay();
        }
        
        // Calculate overtime: overtime_minutes × salary_per_minute × 1.5
        $overtimeAmount = $overtimeMinutes * $salaryPerMinute * 1.5;
        
        // Calculate net salary: gross - deductions + overtime + compensation
        $netSalary = $employee->salary - $totalDeductions + $overtimeAmount + $compensation;
        
        return [
            'gross_salary' => $employee->salary,
            'working_days' => $workingDays,
            'salary_per_day' => $salaryPerDay,
            'salary_per_minute' => $salaryPerMinute,
            'total_deductions' => $totalDeductions,
            'late_deductions' => $lateDeductions,
            'absent_deductions' => $absentDeductions,
            'overtime_minutes' => $overtimeMinutes,
            'overtime_amount' => $overtimeAmount,
            'compensation' => $compensation,
            'net_salary' => $netSalary,
            'daily_details' => $dailyDetails,
            'month_formatted' => $month->format('F Y'),
        ];
    }
}
