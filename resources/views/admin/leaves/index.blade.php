@extends('layouts.app')

@section('title', 'Leave Management')
@section('page-title', 'Leave Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Total Leaves</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Pending</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Approved</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['approved'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Rejected</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
            <p class="text-gray-600 text-sm font-medium mb-1">Total Days</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_days'], 1) }}</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Leaves</h3>
        <form method="GET" action="{{ route('admin.leaves.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All</option>
                        <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $statusFilter === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $statusFilter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <!-- Employee Filter -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select 
                        id="employee_id" 
                        name="employee_id" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="all" {{ $employeeFilter === 'all' ? 'selected' : '' }}>All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $employeeFilter == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ 'DEVNO-' . $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Leave Format Filter -->
                <div>
                    <label for="leave_format" class="block text-sm font-medium text-gray-700 mb-1">Leave Format</label>
                    <select 
                        id="leave_format" 
                        name="leave_format" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="all" {{ $leaveFormatFilter === 'all' ? 'selected' : '' }}>All</option>
                        <option value="casual" {{ $leaveFormatFilter === 'casual' ? 'selected' : '' }}>Casual</option>
                        <option value="medical" {{ $leaveFormatFilter === 'medical' ? 'selected' : '' }}>Medical</option>
                        <option value="annual" {{ $leaveFormatFilter === 'annual' ? 'selected' : '' }}>Annual</option>
                    </select>
                </div>

                <!-- Date From -->
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

                <!-- Date To -->
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

                <!-- Sort Order -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort</label>
                    <select 
                        id="sort" 
                        name="sort" 
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
            </div>

            <div class="flex space-x-2">
                <button 
                    type="submit" 
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                >
                    Apply Filters
                </button>
                <a 
                    href="{{ route('admin.leaves.index') }}" 
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Leaves Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-bold text-gray-900">All Leave Requests</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Applied On</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $leave->employee->name }}</div>
                                <div class="text-xs text-gray-500">{{ 'DEVNO-' . $leave->employee->employee_id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm font-medium text-gray-900">{{ $leave->leave_date->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $leave->leave_date->format('l') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $leave->leave_type === 'full_day' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $leave->leave_type === 'full_day' ? 'Full Day' : 'Half Day' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $leave->leave_format === 'casual' ? 'bg-green-100 text-green-800' : ($leave->leave_format === 'medical' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800') }}">
                                    {{ ucfirst($leave->leave_format) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ number_format($leave->number_of_days, 1) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($leave->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ Approved
                                    </span>
                                @elseif($leave->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ✗ Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        ⏳ Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $leave->description ?? 'No description' }}">
                                    {{ $leave->description ?? '-' }}
                                </div>
                                @if($leave->rejection_reason)
                                    <div class="text-xs text-red-600 mt-1">
                                        Reason: {{ $leave->rejection_reason }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-900">{{ $leave->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $leave->created_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a 
                                        href="{{ route('admin.leaves.show', $leave) }}" 
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                    >
                                        View
                                    </a>
                                    @if($leave->status === 'pending')
                                        <form method="POST" action="{{ route('admin.leaves.approve', $leave) }}" class="inline">
                                            @csrf
                                            <button 
                                                type="submit" 
                                                class="text-green-600 hover:text-green-900 text-sm font-medium"
                                                onclick="return confirm('Are you sure you want to approve this leave request?')"
                                            >
                                                Approve
                                            </button>
                                        </form>
                                        <button 
                                            type="button"
                                            onclick="showRejectModal({{ $leave->id }}, '{{ $leave->employee->name }}')"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium"
                                        >
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm font-medium">No leave requests found</p>
                                    <p class="text-xs text-gray-400 mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($leaves->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Leave Request</h3>
            <p class="text-sm text-gray-600 mb-4">Employee: <span id="rejectEmployeeName" class="font-semibold"></span></p>
            
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Rejection Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="rejection_reason" 
                        name="rejection_reason" 
                        rows="4"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Enter reason for rejection"
                        required
                        maxlength="500"
                    ></textarea>
                    <p class="mt-1 text-xs text-gray-500">Maximum 500 characters</p>
                </div>
                
                <div class="flex items-center justify-end space-x-3">
                    <button 
                        type="button"
                        onclick="closeRejectModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700"
                    >
                        Reject Leave
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showRejectModal(leaveId, employeeName) {
        document.getElementById('rejectEmployeeName').textContent = employeeName;
        document.getElementById('rejectForm').action = `/leaves/${leaveId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectForm').reset();
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('rejectModal');
        if (event.target === modal) {
            closeRejectModal();
        }
    }
</script>
@endsection
