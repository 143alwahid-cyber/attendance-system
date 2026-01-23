@extends('layouts.app')

@section('title', 'Leave Details')
@section('page-title', 'Leave Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Back Button -->
    <div>
        <a 
            href="{{ route('admin.leaves.index') }}" 
            class="inline-flex items-center text-indigo-600 hover:text-indigo-900 text-sm font-medium"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Leave Management
        </a>
    </div>

    <!-- Leave Details Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Leave Request Details</h2>
            <div>
                @if($leave->status === 'approved')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        ✓ Approved
                    </span>
                @elseif($leave->status === 'rejected')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        ✗ Rejected
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        ⏳ Pending
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Employee Information -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Employee</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $leave->employee->name }}</p>
                    <p class="text-sm text-gray-600">{{ 'DEVNO-' . $leave->employee->employee_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Leave Date</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $leave->leave_date->format('F d, Y') }}</p>
                    <p class="text-sm text-gray-600">{{ $leave->leave_date->format('l') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Leave Type</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $leave->leave_type === 'full_day' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $leave->leave_type === 'full_day' ? 'Full Day' : 'Half Day' }}
                    </span>
                </div>
            </div>

            <!-- Leave Details -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Leave Format</label>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $leave->leave_format === 'casual' ? 'bg-green-100 text-green-800' : ($leave->leave_format === 'medical' ? 'bg-red-100 text-red-800' : 'bg-indigo-100 text-indigo-800') }}">
                        {{ ucfirst($leave->leave_format) }} Leave
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Number of Days</label>
                    <p class="text-lg font-semibold text-gray-900">{{ number_format($leave->number_of_days, 1) }} day(s)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Applied On</label>
                    <p class="text-sm text-gray-900">{{ $leave->created_at->format('F d, Y g:i A') }}</p>
                </div>

                @if($leave->approved_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Approved On</label>
                        <p class="text-sm text-gray-900">{{ $leave->approved_at->format('F d, Y g:i A') }}</p>
                    </div>
                @endif

                @if($leave->rejected_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Rejected On</label>
                        <p class="text-sm text-gray-900">{{ $leave->rejected_at->format('F d, Y g:i A') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-900 whitespace-pre-wrap">{{ $leave->description ?? 'No description provided' }}</p>
            </div>
        </div>

        <!-- Rejection Reason -->
        @if($leave->rejection_reason)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <label class="block text-sm font-medium text-red-600 mb-2">Rejection Reason</label>
                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                    <p class="text-red-900 whitespace-pre-wrap">{{ $leave->rejection_reason }}</p>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        @if($leave->status === 'pending')
            <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-end space-x-4">
                <form method="POST" action="{{ route('admin.leaves.approve', $leave) }}" class="inline">
                    @csrf
                    <button 
                        type="submit" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors flex items-center space-x-2"
                        onclick="return confirm('Are you sure you want to approve this leave request?')"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Approve Leave</span>
                    </button>
                </form>
                <button 
                    type="button"
                    onclick="showRejectModal()"
                    class="px-6 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Reject Leave</span>
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
@if($leave->status === 'pending')
<div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Leave Request</h3>
            <p class="text-sm text-gray-600 mb-4">Employee: <span class="font-semibold">{{ $leave->employee->name }}</span></p>
            
            <form method="POST" action="{{ route('admin.leaves.reject', $leave) }}">
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
    function showRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejection_reason').value = '';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('rejectModal');
        if (event.target === modal) {
            closeRejectModal();
        }
    }
</script>
@endif
@endsection
