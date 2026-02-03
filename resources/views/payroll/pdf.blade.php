<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll Statement - {{ $employee->name }}</title>
    <style>
        @page {
            margin: 25mm;
            size: A4 portrait;
        }
        * {
            /* margin: 0;
            padding: 0; */
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            color: #2d3748;
            font-size: 9pt;
            line-height: 1.5;
            background: #ffffff;
            /* padding: 0;
            margin: 0; */
        }
        
        /* Modern Header */
        .header {
            margin-bottom: 30px;
            padding: 20px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border-bottom: 3px solid #3182ce;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 100px;
            height: 3px;
            background: #1e40af;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 65%;
        }
        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 35%;
            padding-left: 20px;
        }
        .logo-section {
            display: inline-block;
            vertical-align: middle;
        }
        .logo-section img {
            height: 55px;
            width: auto;
            vertical-align: middle;
            margin-right: 12px;
        }
        .company-info {
            display: inline-block;
            vertical-align: middle;
        }
        .company-name {
            font-size: 20pt;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 3px;
            letter-spacing: -0.5px;
        }
        .company-tagline {
            font-size: 8.5pt;
            color: #64748b;
            font-weight: 400;
            letter-spacing: 0.3px;
        }
        .document-info {
            background: #ffffff;
            padding: 15px 20px;
            border-radius: 6px;
            border-left: 4px solid #3182ce;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .document-title {
            font-size: 14pt;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .document-date {
            font-size: 9pt;
            color: #64748b;
            font-weight: 500;
        }
        
        /* Employee Info Section */
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 6px 0;
            font-size: 9pt;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 35%;
            color: #718096;
            font-weight: 500;
        }
        .info-table td:last-child {
            width: 65%;
            color: #2d3748;
            font-weight: 400;
        }
        .section-title {
            font-size: 11pt;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Summary Tables */
        .summary-section {
            margin-bottom: 20px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .summary-table thead {
            background: #e6f2ff;
        }
        .summary-table th {
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            color: #2d3748;
            border-bottom: 2px solid #bfdbfe;
        }
        .summary-table th.text-right {
            text-align: right;
        }
        .summary-table td {
            padding: 10px 12px;
            font-size: 9pt;
            color: #2d3748;
            border-bottom: 1px solid #f1f5f9;
        }
        .summary-table td.text-right {
            text-align: right;
            font-weight: 500;
        }
        .summary-table tfoot td {
            background: #e6f2ff;
            font-weight: 600;
            border-top: 2px solid #bfdbfe;
            border-bottom: none;
            padding: 12px;
        }
        
        /* Attendance Table */
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .attendance-table thead {
            background: #e6f2ff;
        }
        .attendance-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 8.5pt;
            color: #2d3748;
            border-bottom: 2px solid #bfdbfe;
        }
        .attendance-table th.text-right {
            text-align: right;
        }
        .attendance-table td {
            padding: 8px;
            color: #2d3748;
            border-bottom: 1px solid #f1f5f9;
        }
        .attendance-table td.text-right {
            text-align: right;
            font-weight: 500;
        }
        .attendance-table tbody tr.absent {
            background: #fff5f5;
        }
        .attendance-table tbody tr.late {
            background: #fffaf0;
        }
        .attendance-table tfoot {
            background: #e6f2ff;
        }
        .attendance-table tfoot td {
            padding: 12px 8px;
            font-weight: 600;
            border-top: 2px solid #bfdbfe;
            border-bottom: none;
        }
        
        /* Status Text */
        .status-text {
            font-size: 8.5pt;
            color: #2d3748;
        }
        .status-absent {
            color: #c53030;
        }
        .status-late {
            color: #c05621;
        }
        .status-present {
            color: #22543d;
        }
        .late-minutes {
            font-size: 7.5pt;
            color: #c05621;
            margin-left: 4px;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 7.5pt;
            color: #a0aec0;
        }
        .footer-line {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <!-- Modern Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo-section">
                    @php
                        $logoPath = public_path('assets/Devno Only Logo.png');
                        $logoExists = file_exists($logoPath);
                        $logoBase64 = '';
                        if ($logoExists) {
                            try {
                                $logoData = file_get_contents($logoPath);
                                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
                            } catch (\Exception $e) {
                                $logoExists = false;
                            }
                        }
                    @endphp
                    @if ($logoExists && $logoBase64)
                        <img src="{{ $logoBase64 }}" alt="DevnoSol Logo">
                    @else
                        <div style="width: 55px; height: 55px; background: #1e40af; border-radius: 6px; display: inline-block; vertical-align: middle; text-align: center; line-height: 55px; color: white; font-weight: 700; font-size: 18px; margin-right: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">DS</div>
                    @endif
                    <div class="company-info">
                        <div class="company-name">DevnoSol</div>
                        <div class="company-tagline">Attendance Management System</div>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="document-info">
                    <div class="document-title">PAYROLL STATEMENT</div>
                    <div class="document-date">{{ $payroll['month_formatted'] }}</div>
                    @if(!empty($payroll['perfect_attendance']))
                        <div style="margin-top: 10px; padding: 8px 14px; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 9999px; font-size: 9pt; font-weight: 600; color: #92400e; text-align: center;">★ 100% Attendance — No Late Check-ins ★</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Information -->
    <div class="info-section">
        <div class="section-title">Employee Details</div>
        <table class="info-table">
            <tr>
                <td>Name:</td>
                <td>{{ $employee->name }}</td>
            </tr>
            <tr>
                <td>Employee ID:</td>
                <td style="font-family: 'Courier New', monospace;">{{ $employee->employee_id }}</td>
            </tr>
            <tr>
                <td>Monthly Salary:</td>
                <td>{{ number_format($employee->salary, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Payroll Period -->
    <div class="info-section">
        <div class="section-title">Payroll Period</div>
        <table class="info-table">
            <tr>
                <td>Period:</td>
                <td>{{ $payroll['month_formatted'] }}</td>
            </tr>
            <tr>
                <td>Working Days:</td>
                <td>{{ $payroll['working_days'] }} days</td>
            </tr>
            <tr>
                <td>Salary per Day:</td>
                <td>{{ number_format($payroll['salary_per_day'], 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Earnings -->
    <div class="summary-section">
        <div class="section-title">Earnings</div>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Base Salary</td>
                    <td class="text-right">{{ number_format($employee->salary, 2) }}</td>
                </tr>
                @if ($payroll['overtime_amount'] > 0)
                <tr>
                    <td>Overtime ({{ number_format($payroll['overtime_minutes'], 0) }} minutes)</td>
                    <td class="text-right">{{ number_format($payroll['overtime_amount'], 2) }}</td>
                </tr>
                @endif
                @if ($payroll['compensation'] > 0)
                <tr>
                    <td>Compensation</td>
                    <td class="text-right">{{ number_format($payroll['compensation'], 2) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Earnings</td>
                    <td class="text-right">{{ number_format($employee->salary + $payroll['overtime_amount'] + $payroll['compensation'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Deductions -->
    <div class="summary-section">
        <div class="section-title">Deductions</div>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if ($payroll['absent_deductions'] > 0)
                <tr>
                    <td>Absent Days</td>
                    <td class="text-right">{{ number_format($payroll['absent_deductions'], 2) }}</td>
                </tr>
                @endif
                @if ($payroll['late_deductions'] > 0)
                <tr>
                    <td>Late Check-in Deductions</td>
                    <td class="text-right">{{ number_format($payroll['late_deductions'], 2) }}</td>
                </tr>
                @endif
                @if ($payroll['tax_amount'] > 0)
                <tr>
                    <td>Tax Deduction</td>
                    <td class="text-right">{{ number_format($payroll['tax_amount'], 2) }}</td>
                </tr>
                @endif
                @if ($payroll['absent_deductions'] == 0 && $payroll['late_deductions'] == 0 && $payroll['tax_amount'] == 0)
                <tr>
                    <td>No Deductions</td>
                    <td class="text-right">0.00</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td>Total Deductions</td>
                    <td class="text-right">{{ number_format($payroll['total_deductions'] + $payroll['tax_amount'], 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Net Pay -->
    <div class="summary-section">
        <table class="summary-table">
            <tfoot>
                <tr>
                    <td style="font-size: 10pt; padding: 14px 12px;"><strong>Net Pay</strong></td>
                    <td class="text-right" style="font-size: 12pt; padding: 14px 12px;"><strong>{{ number_format($payroll['net_salary'], 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Daily Attendance Record -->
    <div class="summary-section">
        <div class="section-title">Daily Attendance Record</div>
        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Date</th>
                    <th style="width: 12%;">Day</th>
                    <th style="width: 20%;">Check In</th>
                    <th style="width: 20%;">Check Out</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 15%;" class="text-right">Deduction</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payroll['daily_details'] as $day)
                    <tr class="{{ $day['is_absent'] ? 'absent' : ($day['is_late'] ? 'late' : '') }}">
                        <td>{{ $day['date_formatted'] }}</td>
                        <td>{{ $day['day_name'] }}</td>
                        <td>
                            @if ($day['checkin'])
                                {{ $day['checkin'] }}
                                @if ($day['is_late'])
                                    <span class="late-minutes">({{ $day['late_minutes'] }}m late)</span>
                                @endif
                            @else
                                <span class="status-text">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($day['checkout'])
                                {{ $day['checkout'] }}
                            @else
                                <span class="status-text">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($day['is_absent'])
                                <span class="status-text status-absent">Absent</span>
                            @elseif ($day['is_late'])
                                <span class="status-text status-late">Late</span>
                            @else
                                <span class="status-text status-present">Present</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if ($day['deduction'] > 0)
                                <span style="color: #c53030;">-{{ number_format($day['deduction'], 2) }}</span>
                            @else
                                <span style="color: #22543d;">0.00</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right"><strong>Total Deductions:</strong></td>
                    <td class="text-right"><strong style="color: #c53030;">-{{ number_format($payroll['total_deductions'], 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-line">
            <strong>Generated on:</strong> {{ date('F d, Y \a\t g:i A') }}
        </div>
        <div class="footer-line" style="font-style: italic; margin-top: 5px;">
            This is a computer-generated document. No signature required.
        </div>
        <div class="footer-line" style="margin-top: 8px;">
            DevnoSol Attendance Management System
        </div>
    </div>
</body>
</html>
