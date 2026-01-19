@extends('layouts.app')

@section('title', 'Payroll - ' . $employee->name)
@section('page-title', 'Payroll Statement')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4 flex items-center justify-end gap-4">
        @if (session('error'))
            <div class="flex-1 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif
        <form method="GET" action="{{ url('/payroll/export') }}" style="display: inline;">
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
            <input type="hidden" name="overtime_minutes" value="{{ $payroll['overtime_minutes'] }}">
            <input type="hidden" name="compensation" value="{{ $payroll['compensation'] }}">
            <button
                type="submit"
                class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none"
            >
                Export PDF
            </button>
        </form>
    </div>

    <!-- Payroll Header -->
    <div class="bg-white shadow rounded-lg p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Payroll Statement</h1>
                <p class="mt-1 text-sm text-gray-500">{{ $payroll['month_formatted'] }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Employee ID</div>
                <div class="text-lg font-semibold text-gray-900 font-mono">{{ $employee->employee_id }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Employee Information</h2>
                <div class="space-y-1 text-sm">
                    <div><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900">{{ $employee->name }}</span></div>
                    <div><span class="text-gray-500">Employee ID:</span> <span class="font-medium text-gray-900 font-mono">{{ $employee->employee_id }}</span></div>
                    <div><span class="text-gray-500">Monthly Salary:</span> <span class="font-medium text-gray-900">{{ number_format($employee->salary, 2) }}</span></div>
                </div>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Payroll Period</h2>
                <div class="space-y-1 text-sm">
                    <div><span class="text-gray-500">Month:</span> <span class="font-medium text-gray-900">{{ $payroll['month_formatted'] }}</span></div>
                    <div><span class="text-gray-500">Working Days:</span> <span class="font-medium text-gray-900">{{ $payroll['working_days'] }} days</span></div>
                    <div><span class="text-gray-500">Salary per Day:</span> <span class="font-medium text-gray-900">{{ number_format($payroll['salary_per_day'], 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-blue-50 rounded-lg shadow p-4">
            <div class="text-sm text-blue-600">Overtime</div>
            <div class="text-xs text-blue-500 mt-1">{{ number_format($payroll['overtime_minutes'], 0) }} minutes</div>
            <div class="text-2xl font-bold text-blue-700 mt-1">{{ number_format($payroll['overtime_amount'], 2) }}</div>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-4">
            <div class="text-sm text-purple-600">Compensation</div>
            <div class="text-2xl font-bold text-purple-700 mt-1">{{ number_format($payroll['compensation'], 2) }}</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4">
            <div class="text-sm text-red-600">Absents</div>
            <div class="text-2xl font-bold text-red-700 mt-1">{{ number_format($payroll['absent_deductions'], 2) }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4">
            <div class="text-sm text-yellow-600">Late Deductions</div>
            <div class="text-2xl font-bold text-yellow-700 mt-1">{{ number_format($payroll['late_deductions'], 2) }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4">
            <div class="text-sm text-green-600">Net Salary</div>
            <div class="text-2xl font-bold text-green-700 mt-1">{{ number_format($payroll['net_salary'], 2) }}</div>
        </div>
    </div>

    <!-- Daily Details Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Daily Attendance & Deductions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Deduction</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($payroll['daily_details'] as $day)
                        <tr class="{{ $day['is_absent'] ? 'bg-red-50' : ($day['is_late'] ? 'bg-yellow-50' : '') }}">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $day['date_formatted'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $day['day_name'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if ($day['checkin'])
                                    {{ $day['checkin'] }}
                                    @if ($day['is_late'])
                                        <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                            Late ({{ $day['late_minutes'] }}m)
                                        </span>
                                    @endif
                                @else
                                    <span class="text-red-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @if ($day['checkout'])
                                    {{ $day['checkout'] }}
                                @else
                                    <span class="text-red-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if ($day['is_absent'])
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                @elseif ($day['is_late'])
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                        Present
                                    </span>
                                @endif
                            </td>
                                    <td class="px-4 py-3 text-sm text-right font-medium {{ $day['deduction'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                        @if ($day['deduction'] > 0)
                                            -{{ number_format($day['deduction'], 2) }}
                                        @else
                                            0.00
                                        @endif
                                    </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">Total Deductions:</td>
                        <td class="px-4 py-3 text-sm font-bold text-red-600 text-right">-{{ number_format($payroll['total_deductions'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Calculation Notes -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">Calculation Notes:</h3>
        <ul class="text-xs text-blue-800 space-y-1 list-disc list-inside">
            <li>Salary per minute = Salary ÷ 22 days ÷ 9 hours ÷ 60 minutes = {{ number_format($payroll['salary_per_minute'], 4) }}</li>
            <li>Late check-in deduction = 60 minutes (base) + actual late minutes × salary per minute</li>
            <li>Absent deduction = 1.5 × daily salary (applied when BOTH check-in AND check-out are missing)</li>
            <li>Overtime = Overtime minutes × salary per minute × 1.5</li>
            <li>Net Salary = Gross Salary - Deductions + Overtime + Compensation</li>
            <li>Working days exclude weekends (Saturday & Sunday)</li>
        </ul>
    </div>
</div>
@endsection
