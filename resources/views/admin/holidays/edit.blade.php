@extends('layouts.app')

@section('title', 'Edit Holiday')
@section('page-title', 'Edit Holiday')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Edit Holiday</h2>
            <p class="text-gray-600">Update holiday information</p>
        </div>

        <form method="POST" action="{{ route('admin.holidays.update', $holiday) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Holiday Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Holiday Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $holiday->name) }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="e.g., Eid ul Fitr, Christmas, New Year"
                    required
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Holiday Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="start_date" 
                        name="start_date" 
                        value="{{ old('start_date', $holiday->start_date->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                    >
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="end_date" 
                        name="end_date" 
                        value="{{ old('end_date', $holiday->end_date->format('Y-m-d')) }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        required
                        min="{{ old('start_date', $holiday->start_date->format('Y-m-d')) }}"
                    >
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Select the date range for the holiday. For single-day holidays, use the same date for both start and end.</p>

            <!-- Recurring Holiday -->
            <div>
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="is_recurring" 
                        value="1"
                        {{ old('is_recurring', $holiday->is_recurring) ? 'checked' : '' }}
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    >
                    <div>
                        <span class="text-sm font-medium text-gray-700">Recurring Holiday</span>
                        <p class="text-xs text-gray-500">Check this if the holiday repeats every year (e.g., Eid, Christmas)</p>
                    </div>
                </label>
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
                    placeholder="Optional description about the holiday"
                    maxlength="1000"
                >{{ old('description', $holiday->description) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Maximum 1000 characters</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a 
                    href="{{ route('admin.holidays.index') }}" 
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
                    <span>Update Holiday</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Update end_date min attribute when start_date changes
    document.getElementById('start_date').addEventListener('change', function() {
        const endDateInput = document.getElementById('end_date');
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
    });
</script>
@endpush

