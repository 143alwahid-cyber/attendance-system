<style>
    @page {
        margin: 20mm;
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'Arial', 'Helvetica', sans-serif;
        color: #1a1a1a;
        font-size: 11pt;
        line-height: 1.5;
    }
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #1e40af;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .logo-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .logo-section img {
        height: 50px;
        width: auto;
    }
    .company-info {
        line-height: 1.4;
    }
    .company-name {
        font-size: 18pt;
        font-weight: bold;
        color: #1e40af;
        margin-bottom: 3px;
    }
    .company-tagline {
        font-size: 9pt;
        color: #6b7280;
    }
    .document-info {
        text-align: right;
    }
    .document-title {
        font-size: 16pt;
        font-weight: bold;
        color: #1a1a1a;
        margin-bottom: 5px;
        letter-spacing: 1px;
    }
    .document-date {
        font-size: 10pt;
        color: #6b7280;
    }
    .employee-section {
        margin-bottom: 25px;
    }
    .employee-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    .employee-info h3,
    .period-info h3 {
        font-size: 12pt;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 8px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 10pt;
    }
    .info-label {
        color: #6b7280;
        font-weight: 500;
    }
    .info-value {
        color: #1a1a1a;
        font-weight: 600;
    }
    .summary-section {
        margin-bottom: 25px;
    }
    .summary-title {
        font-size: 12pt;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
    }
    .summary-card {
        background: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 6px;
        padding: 15px;
        text-align: center;
    }
    .summary-card.overtime { border-color: #3b82f6; background: #eff6ff; }
    .summary-card.compensation { border-color: #9333ea; background: #faf5ff; }
    .summary-card.absent { border-color: #dc2626; background: #fef2f2; }
    .summary-card.late { border-color: #d97706; background: #fffbeb; }
    .summary-card.net { border-color: #16a34a; background: #f0fdf4; }
    .summary-label {
        font-size: 9pt;
        color: #6b7280;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .summary-subtext {
        font-size: 8pt;
        color: #9ca3af;
        margin-bottom: 8px;
    }
    .summary-value {
        font-size: 18pt;
        font-weight: bold;
        color: #1a1a1a;
    }
    .summary-value.overtime { color: #2563eb; }
    .summary-value.compensation { color: #9333ea; }
    .summary-value.absent { color: #dc2626; }
    .summary-value.late { color: #d97706; }
    .summary-value.net { color: #16a34a; }
    .table-section {
        margin-bottom: 25px;
    }
    .table-title {
        font-size: 12pt;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt;
    }
    thead {
        background: #1e40af;
        color: #ffffff;
    }
    th {
        padding: 10px 8px;
        text-align: left;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 8pt;
        letter-spacing: 0.3px;
    }
    th.text-right {
        text-align: right;
    }
    tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }
    tbody tr.absent {
        background: #fef2f2;
    }
    tbody tr.late {
        background: #fffbeb;
    }
    tbody tr.leave {
        background: #eff6ff;
    }
    td {
        padding: 10px 8px;
        color: #1a1a1a;
    }
    td.text-right {
        text-align: right;
        font-weight: 600;
    }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 8pt;
        font-weight: 600;
    }
    .badge-absent {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-late {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-present {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-leave {
        background: #dbeafe;
        color: #1e40af;
    }
    tfoot {
        background: #f3f4f6;
        font-weight: 600;
    }
    tfoot td {
        padding: 12px 8px;
    }
    .footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #e5e7eb;
        text-align: center;
        font-size: 8pt;
        color: #6b7280;
    }
    .footer-line {
        margin: 3px 0;
    }
    .computer-generated {
        font-style: italic;
        color: #9ca3af;
    }
</style>

<!-- Header with Logo -->
<div class="header">
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
            <img src="{{ $logoBase64 }}" alt="DevnoSol Logo" style="height: 50px; width: auto; max-width: 150px;">
        @else
            <div style="width: 50px; height: 50px; background: #1e40af; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">DS</div>
        @endif
        <div class="company-info">
            <div class="company-name">DevnoSol</div>
            <div class="company-tagline">Attendance Management System</div>
        </div>
    </div>
    <div class="document-info">
        <div class="document-title">PAYROLL STATEMENT</div>
        <div class="document-date">{{ $payroll['month_formatted'] }}</div>
    </div>
</div>

<!-- Employee Information -->
<div class="employee-section">
    <div class="employee-grid">
        <div class="employee-info">
            <h3>Employee Details</h3>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $employee->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Employee ID:</span>
                <span class="info-value" style="font-family: monospace;">{{ $employee->employee_id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Monthly Salary:</span>
                <span class="info-value">{{ number_format($employee->salary, 2) }}</span>
            </div>
        </div>
        <div class="period-info">
            <h3>Payroll Period</h3>
            <div class="info-row">
                <span class="info-label">Period:</span>
                <span class="info-value">{{ $payroll['month_formatted'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Working Days:</span>
                <span class="info-value">{{ $payroll['working_days'] }} days</span>
            </div>
            <div class="info-row">
                <span class="info-label">Salary per Day:</span>
                <span class="info-value">{{ number_format($payroll['salary_per_day'], 2) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Summary Section -->
<div class="summary-section">
    <div class="summary-title">Payroll Summary</div>
    <div class="summary-grid">
        <div class="summary-card overtime">
            <div class="summary-label">Overtime</div>
            <div class="summary-subtext">{{ number_format($payroll['overtime_minutes'], 0) }} minutes</div>
            <div class="summary-value overtime">{{ number_format($payroll['overtime_amount'], 2) }}</div>
        </div>
        <div class="summary-card compensation">
            <div class="summary-label">Compensation</div>
            <div class="summary-value compensation">{{ number_format($payroll['compensation'], 2) }}</div>
        </div>
        <div class="summary-card absent">
            <div class="summary-label">Absents</div>
            <div class="summary-value absent">{{ number_format($payroll['absent_deductions'], 2) }}</div>
        </div>
        <div class="summary-card late">
            <div class="summary-label">Late Deductions</div>
            <div class="summary-value late">{{ number_format($payroll['late_deductions'], 2) }}</div>
        </div>
        <div class="summary-card net">
            <div class="summary-label">Net Salary</div>
            <div class="summary-value net">{{ number_format($payroll['net_salary'], 2) }}</div>
        </div>
    </div>
</div>

<!-- Daily Attendance Table -->
<div class="table-section">
    <div class="table-title">Daily Attendance Record</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th class="text-right">Deduction</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payroll['daily_details'] as $day)
                <tr class="{{ isset($day['has_leave']) && $day['has_leave'] ? 'leave' : ($day['is_absent'] ? 'absent' : ($day['is_late'] ? 'late' : '')) }}">
                    <td>{{ $day['date_formatted'] }}</td>
                    <td>{{ $day['day_name'] }}</td>
                    <td>
                        @if (isset($day['has_leave']) && $day['has_leave'])
                            <span style="color: #2563eb;">—</span>
                        @else
                            {{ $day['checkin'] ?? '—' }}
                            @if ($day['is_late'] && $day['checkin'])
                                <span class="badge badge-late">Late ({{ $day['late_minutes'] }}m)</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if (isset($day['has_leave']) && $day['has_leave'])
                            <span style="color: #2563eb;">—</span>
                        @else
                            {{ $day['checkout'] ?? '—' }}
                        @endif
                    </td>
                    <td>
                        @if (isset($day['has_leave']) && $day['has_leave'])
                            <span class="badge badge-leave">Leave ({{ ucfirst($day['leave_type'] ?? 'full_day') }})</span>
                            @if (isset($day['leave_format']))
                                <div style="font-size: 9px; color: #2563eb; margin-top: 2px;">{{ ucfirst($day['leave_format']) }}</div>
                            @endif
                        @elseif ($day['is_absent'])
                            <span class="badge badge-absent">Absent</span>
                        @elseif ($day['is_late'])
                            <span class="badge badge-late">Late</span>
                        @else
                            <span class="badge badge-present">Present</span>
                        @endif
                    </td>
                    <td class="text-right" style="{{ $day['deduction'] > 0 ? 'color: #dc2626;' : '' }}">
                        @if ($day['deduction'] > 0)
                            -{{ number_format($day['deduction'], 2) }}
                        @else
                            0.00
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">Total Deductions:</td>
                <td class="text-right" style="color: #dc2626;">-{{ number_format($payroll['total_deductions'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Footer -->
<div class="footer">
    <div class="footer-line">
        <strong>Generated on:</strong> {{ date('F d, Y \a\t g:i A') }}
    </div>
    <div class="footer-line computer-generated">
        This is a computer-generated document. No signature required.
    </div>
    <div class="footer-line" style="margin-top: 10px; font-size: 7pt; color: #9ca3af;">
        DevnoSol Attendance Management System
    </div>
</div>
