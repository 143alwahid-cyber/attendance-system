@extends('layouts.employee')

@section('title', 'Holidays')
@section('page-title', 'Holidays')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-1">Company Holidays</h1>
                <p class="text-purple-200 text-sm">View all public holidays and company holidays</p>
            </div>
            <div class="text-right">
                <p class="text-purple-200 text-sm">Year</p>
                <p class="text-2xl font-bold">{{ $yearFilter }}</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('employee.holidays.index') }}" class="flex items-center space-x-4">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Select Year</label>
                <select 
                    id="year" 
                    name="year" 
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    onchange="this.form.submit()"
                >
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Holidays List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($holidays as $holiday)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($holiday->display_start_date->format('Y-m-d') === $holiday->display_end_date->format('Y-m-d'))
                                    <div class="text-sm font-medium text-gray-900">{{ $holiday->display_start_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $holiday->display_start_date->format('l') }}</div>
                                @else
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $holiday->display_start_date->format('M d, Y') }} - {{ $holiday->display_end_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $holiday->display_start_date->format('l') }} to {{ $holiday->display_end_date->format('l') }}
                                        <span class="ml-2 text-purple-600">({{ $holiday->days_count }} days)</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $holiday->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-md">
                                    {{ $holiday->description ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($holiday->is_recurring)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        🔄 Recurring
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        📅 One-time
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-medium">No holidays found for {{ $yearFilter }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Information Card -->
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-purple-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-purple-900 mb-2">About Holidays</h3>
                <ul class="text-sm text-purple-800 space-y-1">
                    <li>• Holidays are automatically excluded from working days calculation</li>
                    <li>• No attendance deductions are applied on holiday dates</li>
                    <li>• Recurring holidays apply to the same date every year</li>
                    <li>• Holidays are visible to all employees</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
