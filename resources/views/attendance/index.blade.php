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
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        type="button"
                                        class="edit-time-btn inline-flex items-center rounded-md bg-indigo-100 px-2.5 py-1.5 text-xs font-medium text-indigo-800 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                        data-update-url="{{ route('attendance.update-time', $attendance) }}"
                                        data-date="{{ $attendance->occurred_at->format('Y-m-d') }}"
                                        data-time="{{ $attendance->occurred_at->format('H:i') }}"
                                        data-label="{{ $attendance->employee?->name }} — {{ $attendance->status === 'checkin' ? 'Check In' : 'Check Out' }} ({{ $attendance->occurred_at->format('M d, Y g:i A') }})"
                                    >
                                        Change time
                                    </button>
                                    <button
                                        type="button"
                                        class="view-logs-btn inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1"
                                        data-logs-url="{{ route('attendance.edit-logs', $attendance) }}"
                                        data-label="{{ $attendance->employee?->name }} — {{ $attendance->occurred_at->format('M d, Y g:i A') }}"
                                    >
                                        View edit logs
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
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

    <!-- Edit Time Modal -->
    <div id="editTimeModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="edit-time-title" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true" data-edit-modal-backdrop></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 id="edit-time-title" class="text-lg font-semibold text-gray-900 mb-2">Change time</h3>
                <p id="edit-time-label" class="text-sm text-gray-500 mb-4"></p>
                <form id="editTimeForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    @foreach(request()->only(['employee_id', 'status', 'date_from', 'date_to', 'source_file', 'page']) as $key => $value)
                        @if($value !== null && $value !== '')
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <div class="space-y-4">
                        <div>
                            <label for="occurred_at_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" id="occurred_at_date" name="occurred_at_date" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label for="occurred_at_time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                            <input type="time" id="occurred_at_time" name="occurred_at_time" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" step="60">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button" class="edit-time-cancel inline-flex rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300" data-edit-modal-cancel>Cancel</button>
                        <button type="submit" class="inline-flex rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Logs Modal -->
    <div id="editLogsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="edit-logs-title" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true" data-logs-modal-backdrop></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[80vh] flex flex-col p-6">
                <h3 id="edit-logs-title" class="text-lg font-semibold text-gray-900 mb-2">Edit logs</h3>
                <p id="edit-logs-label" class="text-sm text-gray-500 mb-4"></p>
                <div id="edit-logs-content" class="flex-1 overflow-y-auto min-h-0 border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <p class="text-sm text-gray-400">Loading…</p>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" class="logs-modal-close inline-flex rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300" data-logs-modal-close>Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var editModal = document.getElementById('editTimeModal');
            var editForm = document.getElementById('editTimeForm');
            var editLabel = document.getElementById('edit-time-label');
            var editBackdrop = document.querySelector('[data-edit-modal-backdrop]');
            var editCancel = document.querySelector('[data-edit-modal-cancel]');

            document.querySelectorAll('.edit-time-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    editForm.action = this.getAttribute('data-update-url');
                    document.getElementById('occurred_at_date').value = this.getAttribute('data-date');
                    document.getElementById('occurred_at_time').value = this.getAttribute('data-time');
                    editLabel.textContent = this.getAttribute('data-label');
                    editModal.classList.remove('hidden');
                });
            });

            function closeEditModal() {
                editModal.classList.add('hidden');
            }
            if (editBackdrop) editBackdrop.addEventListener('click', closeEditModal);
            if (editCancel) editCancel.addEventListener('click', closeEditModal);

            var logsModal = document.getElementById('editLogsModal');
            var logsContent = document.getElementById('edit-logs-content');
            var logsLabel = document.getElementById('edit-logs-label');
            var logsBackdrop = document.querySelector('[data-logs-modal-backdrop]');
            var logsClose = document.querySelector('[data-logs-modal-close]');

            document.querySelectorAll('.view-logs-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var url = this.getAttribute('data-logs-url');
                    logsLabel.textContent = this.getAttribute('data-label');
                    logsContent.innerHTML = '<p class="text-sm text-gray-400">Loading…</p>';
                    logsModal.classList.remove('hidden');
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
                        .then(function(r) { return r.text(); })
                        .then(function(html) {
                            logsContent.innerHTML = html;
                        })
                        .catch(function() {
                            logsContent.innerHTML = '<p class="text-sm text-red-600">Failed to load logs.</p>';
                        });
                });
            });

            function closeLogsModal() {
                logsModal.classList.add('hidden');
            }
            if (logsBackdrop) logsBackdrop.addEventListener('click', closeLogsModal);
            if (logsClose) logsClose.addEventListener('click', closeLogsModal);
        })();
    </script>
</div>
@endsection
