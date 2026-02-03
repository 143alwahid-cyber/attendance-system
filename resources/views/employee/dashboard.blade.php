@extends('layouts.employee')

@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Welcome Section with Weather -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Welcome Card -->
        <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl shadow-lg p-6 text-white">
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
            <div class="flex items-center space-x-4 text-indigo-100 mt-4">
                <div class="flex items-center space-x-2 bg-white bg-opacity-10 rounded-lg px-3 py-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span id="current-date" class="text-sm font-medium"></span>
                </div>
                <div class="flex items-center space-x-2 bg-white bg-opacity-10 rounded-lg px-3 py-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="current-time" class="text-sm font-medium"></span>
                </div>
            </div>
        </div>

        <!-- Weather Widget -->
        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white opacity-10 rounded-full"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1" id="weather-city">Loading...</p>
                        <div class="flex items-center space-x-2">
                            <span id="weather-temp" class="text-4xl font-bold">--</span>
                            <span class="text-2xl text-blue-100">°C</span>
                        </div>
                    </div>
                    <div id="weather-icon" class="text-6xl">🌤️</div>
                </div>
                <div class="flex items-center space-x-4 text-sm text-blue-100">
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                        <span id="weather-humidity">--%</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span id="weather-wind">-- km/h</span>
                    </div>
                </div>
                <p id="weather-description" class="text-blue-100 text-sm mt-2 capitalize">--</p>
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

    <!-- Attendance Rate, Grade, and Leaves Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

        <!-- Leaves Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">My Leaves</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $leaveStats['total'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-2 text-xs">
                <div class="text-center">
                    <p class="text-gray-500">Pending</p>
                    <p class="text-lg font-semibold text-yellow-600">{{ $leaveStats['pending'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-500">Approved</p>
                    <p class="text-lg font-semibold text-green-600">{{ $leaveStats['approved'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-gray-500">Days</p>
                    <p class="text-lg font-semibold text-purple-600">{{ number_format($leaveStats['total_days'], 1) }}</p>
                </div>
            </div>
            <a 
                href="{{ route('employee.leaves.index') }}" 
                class="mt-4 block text-center text-sm text-purple-600 hover:text-purple-800 font-medium"
            >
                View All Leaves →
            </a>
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
                                $isHoliday = isset($dayAttendance['is_holiday']) && $dayAttendance['is_holiday'];
                                
                                // Holidays are always displayed
                                if ($isHoliday) {
                                    $shouldDisplay = true;
                                } elseif ($statusFilter === 'present' && !$hasLeave && (!$dayAttendance || (!$dayAttendance['checkin'] && !$dayAttendance['checkout']))) {
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
                                    @if(isset($dayAttendance['is_holiday']) && $dayAttendance['is_holiday'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            🎉 Holiday
                                        </span>
                                        @if(isset($dayAttendance['holiday_name']))
                                            <div class="text-xs text-purple-600 mt-1">{{ $dayAttendance['holiday_name'] }}</div>
                                        @endif
                                    @elseif(isset($dayAttendance['has_leave']) && $dayAttendance['has_leave'])
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
                                    @if(isset($dayAttendance['is_holiday']) && $dayAttendance['is_holiday'])
                                        <span class="text-xs text-purple-600">Public Holiday</span>
                                    @elseif(isset($dayAttendance['has_leave']) && $dayAttendance['has_leave'])
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

<script>
    // Update Date and Time
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', options);
        document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Weather Widget - Using backend API to avoid CORS issues
    async function fetchWeather() {
        try {
            const response = await fetch('/api/weather?city=Lahore');
            const data = await response.json();
            
            document.getElementById('weather-city').textContent = data.city;
            document.getElementById('weather-temp').textContent = data.temp;
            document.getElementById('weather-humidity').textContent = data.humidity + '%';
            document.getElementById('weather-wind').textContent = data.wind + ' km/h';
            document.getElementById('weather-description').textContent = data.description;
            
            // Set weather icon based on condition code
            const weatherCode = data.weatherCode;
            const iconMap = {
                '113': '☀️', '116': '⛅', '119': '☁️', '122': '☁️', '143': '🌫️',
                '176': '🌦️', '179': '🌨️', '182': '🌨️', '185': '🌨️', '200': '⛈️',
                '227': '🌨️', '230': '🌨️', '248': '🌫️', '260': '🌫️', '263': '🌦️',
                '266': '🌦️', '281': '🌨️', '284': '🌨️', '293': '🌦️', '296': '🌦️',
                '299': '🌧️', '302': '🌧️', '305': '🌧️', '308': '🌧️', '311': '🌨️',
                '314': '🌨️', '317': '🌨️', '320': '🌨️', '323': '🌨️', '326': '🌨️',
                '329': '🌨️', '332': '🌨️', '335': '🌨️', '338': '🌨️', '350': '🌨️',
                '353': '🌦️', '356': '🌧️', '359': '🌧️', '362': '🌨️', '365': '🌨️',
                '368': '🌨️', '371': '🌨️', '374': '🌨️', '377': '🌨️', '386': '⛈️',
                '389': '⛈️', '392': '⛈️', '395': '⛈️'
            };
            document.getElementById('weather-icon').textContent = iconMap[weatherCode] || '🌤️';
        } catch (error) {
            console.error('Weather fetch error:', error);
            // Fallback display
            document.getElementById('weather-city').textContent = 'Lahore, PK';
            document.getElementById('weather-temp').textContent = '22';
            document.getElementById('weather-humidity').textContent = '60%';
            document.getElementById('weather-wind').textContent = '10 km/h';
            document.getElementById('weather-description').textContent = 'Partly Cloudy';
            document.getElementById('weather-icon').textContent = '🌤️';
        }
    }
    
    // Fetch weather on page load
    fetchWeather();
    
    // Update weather every 30 minutes
    setInterval(fetchWeather, 30 * 60 * 1000);
</script>
@endsection
