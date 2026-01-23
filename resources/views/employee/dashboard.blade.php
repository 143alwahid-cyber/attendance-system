@extends('layouts.employee')

@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-1">Welcome, {{ $employee->name }}!</h1>
                <p class="text-indigo-200 text-sm">Employee ID: <span class="font-mono">{{ 'DEVNO-' . $employee->employee_id }}</span></p>
            </div>
            <div class="text-right">
                <p class="text-indigo-200 text-sm">Current Month</p>
                <p class="text-2xl font-bold">{{ date('F Y', strtotime($selectedMonth)) }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Total Working Days</p>
            <p class="text-3xl font-bold text-gray-900">{{ $attendanceStats['total_days'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Present Days</p>
            <p class="text-3xl font-bold text-gray-900">{{ $attendanceStats['present_days'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Absent Days</p>
            <p class="text-3xl font-bold text-gray-900">{{ $attendanceStats['absent_days'] }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Late Days</p>
            <p class="text-3xl font-bold text-gray-900">{{ $attendanceStats['late_days'] }}</p>
        </div>
    </div>

    <!-- Attendance Rate and Grade -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">Attendance Rate</p>
                    <p class="text-4xl font-bold text-gray-900">{{ $attendanceStats['attendance_rate'] }}%</p>
                </div>
                <div class="text-6xl">📊</div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">Performance Grade</p>
                    <div class="mt-2">
                        @if($grade === 'Good')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-green-100 text-green-800 border border-green-200">
                                ✓ Good
                            </span>
                        @elseif($grade === 'Average')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                ⚠ Average
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold bg-red-100 text-red-800 border border-red-200">
                                ✗ Bad
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-6xl">
                    @if($grade === 'Good')
                        🎉
                    @elseif($grade === 'Average')
                        📈
                    @else
                        📉
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Attendance Records</h3>
        <form method="GET" action="{{ route('employee.dashboard') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Month Filter -->
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <input 
                        type="month" 
                        id="month" 
                        name="month" 
                        value="{{ $selectedMonth }}" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>

                <!-- Date Range: From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input 
                        type="date" 
                        id="date_from" 
                        name="date_from" 
                        value="{{ $dateFrom }}" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>

                <!-- Date Range: To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input 
                        type="date" 
                        id="date_to" 
                        name="date_to" 
                        value="{{ $dateTo }}" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                        <option value="present" {{ $statusFilter === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ $statusFilter === 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ $statusFilter === 'late' ? 'selected' : '' }}>Late</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Sort Order -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <select 
                        id="sort" 
                        name="sort" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-end space-x-2">
                    <button 
                        type="submit" 
                        class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                    >
                        Apply Filters
                    </button>
                    <a 
                        href="{{ route('employee.dashboard') }}" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                    >
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-bold text-gray-900">My Attendance Records</h2>
            <p class="text-sm text-gray-600 mt-1">
                @if($dateFrom && $dateTo)
                    Attendance details from {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                @else
                    Attendance details for {{ date('F Y', strtotime($selectedMonth)) }}
                @endif
                @if($statusFilter !== 'all')
                    | Filtered by: <span class="font-semibold capitalize">{{ $statusFilter }}</span>
                @endif
            </p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        // Determine date range for display
                        if ($dateFrom && $dateTo) {
                            $displayStartDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
                            $displayEndDate = \Carbon\Carbon::parse($dateTo)->endOfDay();
                        } else {
                            $displayStartDate = \Carbon\Carbon::parse($selectedMonth)->startOfMonth();
                            $displayEndDate = \Carbon\Carbon::parse($selectedMonth)->endOfMonth();
                        }
                        
                        $attendanceByDate = $attendanceStats['attendance_by_date'] ?? [];
                        $currentDate = $displayStartDate->copy();
                        $displayedCount = 0;
                    @endphp
                    @while($currentDate <= $displayEndDate)
                        @php
                            $dayOfWeek = $currentDate->dayOfWeek;
                            $isWeekend = ($dayOfWeek == \Carbon\Carbon::SATURDAY || $dayOfWeek == \Carbon\Carbon::SUNDAY);
                            $dateKey = $currentDate->format('Y-m-d');
                            $dayAttendance = $attendanceByDate[$dateKey] ?? null;
                            
                            // Apply status filter
                            $shouldDisplay = true;
                            if ($statusFilter !== 'all' && !$isWeekend) {
                                $hasLeave = isset($dayAttendance['has_leave']) && $dayAttendance['has_leave'];
                                if ($statusFilter === 'present' && !$hasLeave && (!$dayAttendance || (!$dayAttendance['checkin'] && !$dayAttendance['checkout']))) {
                                    $shouldDisplay = false;
                                } elseif ($statusFilter === 'absent' && !$hasLeave && $dayAttendance && ($dayAttendance['checkin'] || $dayAttendance['checkout'])) {
                                    $shouldDisplay = false;
                                } elseif ($statusFilter === 'late' && (!$dayAttendance || !$dayAttendance['late'])) {
                                    $shouldDisplay = false;
                                }
                            }
                        @endphp
                        @if(!$isWeekend && $shouldDisplay)
                            @php $displayedCount++; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $currentDate->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $currentDate->format('l') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(isset($dayAttendance['has_leave']) && $dayAttendance['has_leave'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Leave ({{ ucfirst($dayAttendance['leave_type'] ?? 'full_day') }})
                                        </span>
                                        @if(isset($dayAttendance['leave_format']))
                                            <div class="text-xs text-gray-500 mt-1">{{ ucfirst($dayAttendance['leave_format']) }}</div>
                                        @endif
                                    @elseif($dayAttendance && ($dayAttendance['checkin'] || $dayAttendance['checkout']))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Present
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Absent
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($dayAttendance && $dayAttendance['checkin'])
                                        <span class="text-sm text-gray-900">
                                            {{ $dayAttendance['checkin_time']->format('g:i A') }}
                                        </span>
                                        @if($dayAttendance['late'])
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Late
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($dayAttendance && $dayAttendance['checkout'])
                                        <span class="text-sm text-gray-900">
                                            {{ $dayAttendance['checkout_time']->format('g:i A') }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if(isset($dayAttendance['has_leave']) && $dayAttendance['has_leave'])
                                        <span class="text-xs text-blue-600">Approved Leave</span>
                                    @elseif($dayAttendance && $dayAttendance['late'])
                                        <span class="text-xs text-yellow-600">Late arrival</span>
                                    @elseif(!$dayAttendance || (!$dayAttendance['checkin'] && !$dayAttendance['checkout']))
                                        <span class="text-xs text-red-600">No attendance</span>
                                    @else
                                        <span class="text-xs text-green-600">On time</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @php
                            $currentDate->addDay();
                        @endphp
                    @endwhile
                    @if($displayedCount === 0)
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">No attendance records found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
