@extends('layouts.app')

@section('title', 'Attendance Records')
@section('page-title', 'Attendance Records')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header with Upload Button -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Records</h1>
            <p class="text-sm text-gray-600 mt-1">View and manage all attendance data</p>
        </div>
        <a 
            href="{{ route('attendance.upload') }}" 
            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-0"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Upload Attendance
        </a>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
        <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Employee Filter -->
            <div>
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select 
                    id="employee_id" 
                    name="employee_id" 
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-300"
                >
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                            {{ $employee->name }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-300"
                >
                    <option value="">All Status</option>
                    <option value="checkin" {{ request('status') == 'checkin' ? 'selected' : '' }}>Check In</option>
                    <option value="checkout" {{ request('status') == 'checkout' ? 'selected' : '' }}>Check Out</option>
                </select>
            </div>

            <!-- Date From Filter -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input 
                    type="date" 
                    id="date_from" 
                    name="date_from" 
                    value="{{ request('date_from') }}"
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-300"
                >
            </div>

            <!-- Date To Filter -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input 
                    type="date" 
                    id="date_to" 
                    name="date_to" 
                    value="{{ request('date_to') }}"
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-300"
                >
            </div>

            <!-- Source File Filter -->
            <div>
                <label for="source_file" class="block text-sm font-medium text-gray-700 mb-1">Source File</label>
                <select 
                    id="source_file" 
                    name="source_file" 
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-300"
                >
                    <option value="">All Files</option>
                    @foreach($sourceFiles as $file)
                        <option value="{{ $file }}" {{ request('source_file') == $file ? 'selected' : '' }}>
                            {{ $file }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-2 lg:col-span-5 flex items-end space-x-3">
                <button 
                    type="submit" 
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-0"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Apply Filters
                </button>
                <a 
                    href="{{ route('attendance.index') }}" 
                    class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-0"
                >
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
            <div class="text-sm text-gray-600 mb-1">Total Records</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($totalCount) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-600 mb-1">Check Ins</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($checkinCount) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-yellow-500">
            <div class="text-sm text-gray-600 mb-1">Check Outs</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($checkoutCount) }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-purple-500">
            <div class="text-sm text-gray-600 mb-1">Showing</div>
            <div class="text-2xl font-bold text-gray-900">{{ $attendances->count() }} / {{ number_format($totalCount) }}</div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-bold text-gray-900">All Attendance Records</h2>
            <p class="text-sm text-gray-600 mt-1">View and filter all uploaded attendance records</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attendances as $attendance)
                        @php
                            $occurredAt = Carbon\Carbon::parse($attendance->occurred_at);
                            $isLate = $attendance->status === 'checkin' && $occurredAt->format('H:i') > '10:00';
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors {{ $isLate ? 'bg-yellow-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $occurredAt->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $occurredAt->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->employee)
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->employee->name }}</div>
                                @else
                                    <div class="text-sm text-red-600">Employee Not Found</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->employee)
                                    <div class="text-sm text-gray-900 font-mono">{{ $attendance->employee->employee_id }}</div>
                                @else
                                    <div class="text-sm text-gray-400">—</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    @if($attendance->status === 'checkin')
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            Check In
                                        </span>
                                        @if($isLate)
                                            <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                                Late
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Check Out
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($attendance->source_file)
                                    <div class="text-sm text-gray-900 font-mono truncate max-w-xs" title="{{ $attendance->source_file }}">
                                        {{ $attendance->source_file }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400">—</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $attendance->created_at->format('M d, Y g:i A') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2 text-sm">No attendance records found.</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your filters or upload attendance data.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
