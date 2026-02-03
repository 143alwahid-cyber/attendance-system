@extends('layouts.app')

@section('title', 'Holiday Management')
@section('page-title', 'Holiday Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Holidays</h2>
            <p class="text-gray-600 mt-1">Manage company holidays that affect attendance and payroll</p>
        </div>
        <a
            href="{{ route('admin.holidays.create') }}"
            class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>Add Holiday</span>
        </a>
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

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <form method="GET" action="{{ route('admin.holidays.index') }}" class="flex items-center space-x-4">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select 
                    id="year" 
                    name="year" 
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $yearFilter == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort</label>
                <select 
                    id="sort" 
                    name="sort" 
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Date (Ascending)</option>
                    <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Date (Descending)</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button 
                    type="submit" 
                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                >
                    Apply Filters
                </button>
                <a 
                    href="{{ route('admin.holidays.index') }}" 
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Holidays Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($holidays as $holiday)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($holiday->start_date->format('Y-m-d') === $holiday->end_date->format('Y-m-d'))
                                    <div class="text-sm font-medium text-gray-900">{{ $holiday->start_date->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $holiday->start_date->format('l') }}</div>
                                @else
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $holiday->start_date->format('M d, Y') }} - {{ $holiday->end_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $holiday->start_date->format('l') }} to {{ $holiday->end_date->format('l') }}
                                        <span class="ml-2 text-purple-600">({{ $holiday->days_count }} days)</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $holiday->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-md truncate" title="{{ $holiday->description ?? 'No description' }}">
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
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a 
                                        href="{{ route('admin.holidays.edit', $holiday) }}" 
                                        class="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                                    >
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.holidays.destroy', $holiday) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this holiday?');">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-medium">No holidays found</p>
                                    <p class="text-xs text-gray-400 mt-1">Add a new holiday to get started</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($holidays->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $holidays->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
