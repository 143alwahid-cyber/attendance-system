@extends('layouts.employee')

@section('title', 'Apply for Leave')
@section('page-title', 'Apply for Leave')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Apply for Leave</h2>
            <p class="text-gray-600">Fill in the details below to submit your leave request</p>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('employee.leaves.store') }}" class="space-y-6">
            @csrf

            <!-- Leave Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Leave Type <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4" id="leave_type_container">
                    <label for="leave_type_full_day" class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors leave-type-option {{ old('leave_type') === 'full_day' || !old('leave_type') ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                        <input 
                            type="radio" 
                            name="leave_type" 
                            value="full_day" 
                            id="leave_type_full_day"
                            class="sr-only"
                            {{ old('leave_type') === 'full_day' || !old('leave_type') ? 'checked' : '' }}
                            required
                        >
                        <div class="flex items-center justify-center w-full">
                            <div class="flex items-center mr-3">
                                <div class="w-4 h-4 rounded-full border-2 {{ old('leave_type') === 'full_day' || !old('leave_type') ? 'border-indigo-600 bg-indigo-600' : 'border-gray-400' }} flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full {{ old('leave_type') === 'full_day' || !old('leave_type') ? 'bg-white' : 'bg-transparent' }}"></div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium text-gray-900">Full Day</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">1 day deducted</p>
                            </div>
                        </div>
                    </label>

                    <label for="leave_type_half_day" class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors leave-type-option {{ old('leave_type') === 'half_day' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300' }}">
                        <input 
                            type="radio" 
                            name="leave_type" 
                            value="half_day" 
                            id="leave_type_half_day"
                            class="sr-only"
                            {{ old('leave_type') === 'half_day' ? 'checked' : '' }}
                        >
                        <div class="flex items-center justify-center w-full">
                            <div class="flex items-center mr-3">
                                <div class="w-4 h-4 rounded-full border-2 {{ old('leave_type') === 'half_day' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-400' }} flex items-center justify-center">
                                    <div class="w-2 h-2 rounded-full {{ old('leave_type') === 'half_day' ? 'bg-white' : 'bg-transparent' }}"></div>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-medium text-gray-900">Half Day</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">0.5 day deducted</p>
                            </div>
                        </div>
                    </label>
                </div>
                @error('leave_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Leave Date -->
            <div>
                <label for="leave_date" class="block text-sm font-medium text-gray-700 mb-2">
                    Leave Date <span class="text-red-500">*</span>
                </label>
                <input 
                    type="date" 
                    id="leave_date" 
                    name="leave_date" 
                    value="{{ old('leave_date') }}"
                    min="{{ date('Y-m-d') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required
                >
                <p class="mt-1 text-xs text-gray-500">Select the date for your leave</p>
                @error('leave_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Leave Format -->
            <div>
                <label for="leave_format" class="block text-sm font-medium text-gray-700 mb-2">
                    Leave Format <span class="text-red-500">*</span>
                </label>
                <select 
                    id="leave_format" 
                    name="leave_format" 
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    required
                >
                    <option value="">Select leave format</option>
                    <option value="casual" {{ old('leave_format') === 'casual' ? 'selected' : '' }}>Casual Leave</option>
                    <option value="medical" {{ old('leave_format') === 'medical' ? 'selected' : '' }}>Medical Leave</option>
                    <option value="annual" {{ old('leave_format') === 'annual' ? 'selected' : '' }}>Annual Leave</option>
                </select>
                <div class="mt-2 grid grid-cols-3 gap-2 text-xs">
                    <div class="p-2 bg-green-50 rounded border border-green-200">
                        <span class="font-medium text-green-800">Casual</span>
                        <p class="text-green-600">Personal reasons</p>
                    </div>
                    <div class="p-2 bg-red-50 rounded border border-red-200">
                        <span class="font-medium text-red-800">Medical</span>
                        <p class="text-red-600">Health issues</p>
                    </div>
                    <div class="p-2 bg-indigo-50 rounded border border-indigo-200">
                        <span class="font-medium text-indigo-800">Annual</span>
                        <p class="text-indigo-600">Planned vacation</p>
                    </div>
                </div>
                @error('leave_format')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Enter reason for leave (optional)"
                    maxlength="1000"
                >{{ old('description') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a 
                    href="{{ route('employee.leaves.index') }}" 
                    class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                >
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Submit Leave Request</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Leave Application Guidelines</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Leave requests must be submitted at least one day in advance</li>
                    <li>• You cannot apply for leave on a date that already has a pending or approved leave</li>
                    <li>• Half-day leaves count as 0.5 days, full-day leaves count as 1 day</li>
                    <li>• Your leave request will be reviewed by the admin</li>
                    <li>• You will be notified once your request is approved or rejected</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Update border styling and radio indicator when radio buttons change
    function updateLeaveTypeSelection() {
        document.querySelectorAll('input[name="leave_type"]').forEach(radio => {
            const label = document.querySelector(`label[for="${radio.id}"]`);
            const indicator = label.querySelector('.w-4.h-4.rounded-full');
            const dot = indicator.querySelector('.w-2.h-2.rounded-full');
            
            if (radio.checked) {
                // Update label styling
                label.classList.remove('border-gray-300');
                label.classList.add('border-indigo-500', 'bg-indigo-50');
                
                // Update radio indicator
                indicator.classList.remove('border-gray-400');
                indicator.classList.add('border-indigo-600', 'bg-indigo-600');
                dot.classList.remove('bg-transparent');
                dot.classList.add('bg-white');
            } else {
                // Reset label styling
                label.classList.remove('border-indigo-500', 'bg-indigo-50');
                label.classList.add('border-gray-300');
                
                // Reset radio indicator
                indicator.classList.remove('border-indigo-600', 'bg-indigo-600');
                indicator.classList.add('border-gray-400');
                dot.classList.remove('bg-white');
                dot.classList.add('bg-transparent');
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLeaveTypeSelection();
        
        // Add event listeners to all radio buttons
        document.querySelectorAll('input[name="leave_type"]').forEach(radio => {
            radio.addEventListener('change', updateLeaveTypeSelection);
            
            // Also listen for click on the label
            const label = document.querySelector(`label[for="${radio.id}"]`);
            if (label) {
                label.addEventListener('click', function(e) {
                    // Small delay to ensure radio is checked
                    setTimeout(updateLeaveTypeSelection, 10);
                });
            }
        });
    });
</script>
@endsection
