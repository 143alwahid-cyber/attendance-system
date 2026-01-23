<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll as PayrollModel;
use App\Models\Leave;
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
    
    /**
     * Calculate monthly tax based on Pakistan tax brackets
     * Returns monthly tax amount in PKR
     */
    private function calculateTax(float $monthlySalary): float
    {
        // Tax brackets: [monthly_salary => monthly_tax]
        $taxBrackets = [
            50000 => 0,
            55000 => 50,
            60000 => 100,
            70000 => 200,
            80000 => 300,
            90000 => 400,
            100000 => 500,
            120000 => 1533,
            150000 => 3967,
            200000 => 7967,
            250000 => 17967,
            300000 => 27167,
            350000 => 33750,
            400000 => 48333,
            500000 => 81667,
            750000 => 181667,
            833333 => 216667,
        ];

        // If salary is below minimum bracket, no tax
        if ($monthlySalary < 50000) {
            return 0;
        }

        // Find the appropriate bracket
        $prevBracket = null;
        $prevTax = 0;
        
        foreach ($taxBrackets as $bracketSalary => $bracketTax) {
            if ($monthlySalary == $bracketSalary) {
                return $bracketTax;
            }
            
            if ($monthlySalary < $bracketSalary) {
                // Interpolate between previous and current bracket
                if ($prevBracket !== null) {
                    $salaryDiff = $monthlySalary - $prevBracket;
                    $bracketDiff = $bracketSalary - $prevBracket;
                    $taxDiff = $bracketTax - $prevTax;
                    
                    // Linear interpolation
                    $interpolatedTax = $prevTax + (($salaryDiff / $bracketDiff) * $taxDiff);
                    return round($interpolatedTax, 2);
                }
            }
            
            $prevBracket = $bracketSalary;
            $prevTax = $bracketTax;
        }

        // If salary exceeds highest bracket, use highest bracket tax
        // For salaries above 833,333, calculate based on highest bracket rate
        if ($monthlySalary > 833333) {
            // Extrapolate: use the rate from the last bracket
            // Approximate: tax increases roughly proportionally
            $highestBracket = 833333;
            $highestTax = 216667;
            $rate = $highestTax / $highestBracket;
            return round($monthlySalary * $rate, 2);
        }

        return 0;
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
        
        // Get all approved leaves for the month
        $approvedLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->whereBetween('leave_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(function ($leave) {
                return $leave->leave_date->format('Y-m-d');
            });
        
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
            
            // Check if there's an approved leave for this date
            $approvedLeave = $approvedLeaves->get($dateKey);
            $hasFullDayLeave = $approvedLeave && $approvedLeave->leave_type === 'full_day';
            $hasHalfDayLeave = $approvedLeave && $approvedLeave->leave_type === 'half_day';
            $leaveFormat = $approvedLeave ? $approvedLeave->leave_format : null;
            
            $dayDeduction = 0;
            $isAbsent = false;
            $isLate = false;
            $lateMinutes = 0;
            
            // If there's a full day approved leave, no deductions at all
            if ($hasFullDayLeave) {
                // Full day leave: no absent deduction, no late deduction
                $isAbsent = false;
                $isLate = false;
            } elseif ($hasHalfDayLeave) {
                // Half day leave: no absent deduction, but can still have late deduction if they came late
                // Only check for late if checkin exists and they came late
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
                // No absent deduction for half day leave
                $isAbsent = false;
            } else {
                // No approved leave: normal attendance logic
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
                'has_leave' => $approvedLeave !== null,
                'leave_type' => $approvedLeave ? $approvedLeave->leave_type : null,
                'leave_format' => $leaveFormat,
            ];
            
            $currentDate->addDay();
        }
        
        // Calculate overtime: overtime_minutes × salary_per_minute × 1.5
        $overtimeAmount = $overtimeMinutes * $salaryPerMinute * 1.5;
        
        // Calculate tax based on gross salary
        $taxAmount = $this->calculateTax($employee->salary);
        
        // Calculate net salary: gross - deductions - tax + overtime + compensation
        $netSalary = $employee->salary - $totalDeductions - $taxAmount + $overtimeAmount + $compensation;
        
        return [
            'gross_salary' => $employee->salary,
            'working_days' => $workingDays,
            'salary_per_day' => $salaryPerDay,
            'salary_per_minute' => $salaryPerMinute,
            'total_deductions' => $totalDeductions,
            'late_deductions' => $lateDeductions,
            'absent_deductions' => $absentDeductions,
            'tax_amount' => $taxAmount,
            'overtime_minutes' => $overtimeMinutes,
            'overtime_amount' => $overtimeAmount,
            'compensation' => $compensation,
            'net_salary' => $netSalary,
            'daily_details' => $dailyDetails,
            'month_formatted' => $month->format('F Y'),
        ];
    }

    /**
     * Save a generated payroll to the database.
     */
    public function savePayroll(Request $request): RedirectResponse
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

        // Check if payroll already exists for this employee and month
        $existingPayroll = PayrollModel::where('employee_id', $employee->id)
            ->where('payroll_month', $month->copy()->startOfMonth())
            ->first();

        if ($existingPayroll) {
            // Update existing payroll
            $existingPayroll->update([
                'gross_salary' => $payroll['gross_salary'],
                'working_days' => $payroll['working_days'],
                'salary_per_day' => $payroll['salary_per_day'],
                'salary_per_minute' => $payroll['salary_per_minute'],
                'total_deductions' => $payroll['total_deductions'],
                'late_deductions' => $payroll['late_deductions'],
                'absent_deductions' => $payroll['absent_deductions'],
                'tax_amount' => $payroll['tax_amount'],
                'overtime_minutes' => $payroll['overtime_minutes'],
                'overtime_amount' => $payroll['overtime_amount'],
                'compensation' => $payroll['compensation'],
                'net_salary' => $payroll['net_salary'],
                'daily_details' => $payroll['daily_details'],
            ]);

            return redirect()
                ->route('payroll.saved')
                ->with('status', 'Payroll updated successfully for ' . $employee->name . ' - ' . $payroll['month_formatted']);
        }

        // Create new payroll
        PayrollModel::create([
            'employee_id' => $employee->id,
            'payroll_month' => $month->copy()->startOfMonth(),
            'gross_salary' => $payroll['gross_salary'],
            'working_days' => $payroll['working_days'],
            'salary_per_day' => $payroll['salary_per_day'],
            'salary_per_minute' => $payroll['salary_per_minute'],
            'total_deductions' => $payroll['total_deductions'],
            'late_deductions' => $payroll['late_deductions'],
            'absent_deductions' => $payroll['absent_deductions'],
            'tax_amount' => $payroll['tax_amount'],
            'overtime_minutes' => $payroll['overtime_minutes'],
            'overtime_amount' => $payroll['overtime_amount'],
            'compensation' => $payroll['compensation'],
            'net_salary' => $payroll['net_salary'],
            'daily_details' => $payroll['daily_details'],
        ]);

        return redirect()
            ->route('payroll.saved')
            ->with('status', 'Payroll saved successfully for ' . $employee->name . ' - ' . $payroll['month_formatted']);
    }

    /**
     * Display all saved payrolls (Admin view).
     */
    public function savedPayrolls(Request $request): View
    {
        $query = PayrollModel::with('employee')->orderBy('payroll_month', 'desc')->orderBy('created_at', 'desc');

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by month
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month)->startOfMonth();
            $query->where('payroll_month', $month);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('payroll_month', '>=', Carbon::parse($request->date_from)->startOfMonth());
        }

        if ($request->filled('date_to')) {
            $query->where('payroll_month', '<=', Carbon::parse($request->date_to)->startOfMonth());
        }

        $payrolls = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('name')->get();

        return view('payroll.saved', compact('payrolls', 'employees'));
    }

    /**
     * Display employee's saved payrolls (Employee view).
     */
    public function employeePayrolls(Request $request): View
    {
        $employee = auth('employee')->user();

        $query = PayrollModel::where('employee_id', $employee->id)
            ->orderBy('payroll_month', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter by month
        if ($request->filled('month')) {
            $month = Carbon::parse($request->month)->startOfMonth();
            $query->where('payroll_month', $month);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('payroll_month', '>=', Carbon::parse($request->date_from)->startOfMonth());
        }

        if ($request->filled('date_to')) {
            $query->where('payroll_month', '<=', Carbon::parse($request->date_to)->startOfMonth());
        }

        $payrolls = $query->paginate(20)->withQueryString();

        return view('payroll.employee-list', compact('payrolls', 'employee'));
    }

    /**
     * Download a saved payroll as PDF.
     */
    public function downloadSavedPayroll(PayrollModel $payroll)
    {
        $employee = $payroll->employee;
        $month = Carbon::parse($payroll->payroll_month);

        // Convert saved payroll data to the format expected by the PDF view
        $payrollData = [
            'gross_salary' => $payroll->gross_salary,
            'working_days' => $payroll->working_days,
            'salary_per_day' => $payroll->salary_per_day,
            'salary_per_minute' => $payroll->salary_per_minute,
            'total_deductions' => $payroll->total_deductions,
            'late_deductions' => $payroll->late_deductions,
            'absent_deductions' => $payroll->absent_deductions,
            'tax_amount' => $payroll->tax_amount,
            'overtime_minutes' => $payroll->overtime_minutes,
            'overtime_amount' => $payroll->overtime_amount,
            'compensation' => $payroll->compensation,
            'net_salary' => $payroll->net_salary,
            'daily_details' => $payroll->daily_details,
            'month_formatted' => $month->format('F Y'),
        ];

        // Use dompdf for backend PDF generation
        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            try {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.pdf', [
                    'employee' => $employee,
                    'month' => $month,
                    'payroll' => $payrollData
                ])
                    ->setPaper('a4', 'portrait')
                    ->setOption('enable-local-file-access', true)
                    ->setOption('isRemoteEnabled', true);

                $fileName = 'Payroll_' . $employee->employee_id . '_' . $month->format('Y-m') . '.pdf';

                return $pdf->download($fileName);
            } catch (\Exception $e) {
                \Log::error('PDF generation error: ' . $e->getMessage());
                return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'PDF generation package is not installed.');
    }

    /**
     * View a saved payroll (Admin view).
     */
    public function viewSavedPayroll(PayrollModel $payroll): View
    {
        $employee = $payroll->employee;
        $month = Carbon::parse($payroll->payroll_month);
        $savedPayroll = $payroll; // Keep the model instance

        // Convert saved payroll data to the format expected by the view
        $payroll = [
            'gross_salary' => $savedPayroll->gross_salary,
            'working_days' => $savedPayroll->working_days,
            'salary_per_day' => $savedPayroll->salary_per_day,
            'salary_per_minute' => $savedPayroll->salary_per_minute,
            'total_deductions' => $savedPayroll->total_deductions,
            'late_deductions' => $savedPayroll->late_deductions,
            'absent_deductions' => $savedPayroll->absent_deductions,
            'tax_amount' => $savedPayroll->tax_amount,
            'overtime_minutes' => $savedPayroll->overtime_minutes,
            'overtime_amount' => $savedPayroll->overtime_amount,
            'compensation' => $savedPayroll->compensation,
            'net_salary' => $savedPayroll->net_salary,
            'daily_details' => $savedPayroll->daily_details,
            'month_formatted' => $month->format('F Y'),
        ];

        return view('payroll.show', compact('employee', 'month', 'payroll', 'savedPayroll'));
    }
}
