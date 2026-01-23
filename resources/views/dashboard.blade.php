@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Welcome Section with Date, Time, and Weather -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Welcome Card with Animated Logo -->
        <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-xl shadow-lg p-6 text-white relative overflow-hidden">
            <!-- Animated Coding Background Elements -->
            <div class="absolute top-4 right-8 text-white opacity-5 text-6xl font-mono code-float-1">{ }</div>
            <div class="absolute top-16 right-20 text-white opacity-5 text-4xl font-mono code-float-2">&lt;/&gt;</div>
            <div class="absolute bottom-8 left-12 text-white opacity-5 text-5xl font-mono code-float-3">#</div>
            <div class="absolute bottom-16 left-24 text-white opacity-5 text-3xl font-mono code-float-4">()</div>
            <div class="absolute top-1/2 right-1/4 text-white opacity-5 text-4xl font-mono code-float-5">[]</div>
            <div class="absolute top-1/3 left-1/4 text-white opacity-5 text-3xl font-mono code-float-6">=&gt;</div>
            
            <div class="flex items-center justify-between relative z-10">
                <div class="flex-1">
                    <div class="flex items-center space-x-4 mb-3">
                        <!-- Animated DevnoSol Logo -->
                        <div class="welcome-logo-container">
                            @php
                                $logoPath = public_path('assets/Devno Only Logo.png');
                                $logoExists = file_exists($logoPath);
                            @endphp
                            @if ($logoExists)
                                <img 
                                    src="{{ asset('assets/Devno Only Logo.png') }}" 
                                    alt="DevnoSol Logo" 
                                    class="welcome-logo h-16 w-auto"
                                >
                            @else
                                <div class="welcome-logo-text">DS</div>
                            @endif
                            <!-- Waving Hand Emoji -->
                            <span class="waving-hand text-4xl ml-2">👋</span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold mb-1">Welcome back, {{ auth('web')->user()->name }}!</h1>
                            <p class="text-indigo-200 text-sm">Have a productive day! ✨</p>
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Employees Card -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalEmployees }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Absents Card with Month Filter -->
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">Total Absents</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalAbsents }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center space-x-2">
                <label for="month" class="text-xs text-gray-600">Filter by Month:</label>
                <input 
                    type="month" 
                    id="month" 
                    name="month" 
                    value="{{ $selectedMonth }}" 
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    onchange="this.form.submit()"
                >
            </form>
        </div>
    </div>

    <!-- Employees Table with Attendance Records -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-bold text-gray-900">Employee Attendance Overview</h2>
            <p class="text-sm text-gray-600 mt-1">Attendance records and performance grades for {{ date('F Y', strtotime($selectedMonth)) }}</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Days</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Late</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $emp['name'] }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($emp['salary'], 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-mono">{{ $emp['employee_id'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ $emp['total_days'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $emp['present_days'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $emp['absent_days'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $emp['late_days'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-medium text-gray-900">{{ $emp['attendance_rate'] }}%</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($emp['grade'] === 'Good')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                        ✓ Good
                                    </span>
                                @elseif($emp['grade'] === 'Average')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        ⚠ Average
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                        ✗ Bad
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No employees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Welcome Logo Animations */
    .welcome-logo-container {
        display: flex;
        align-items: center;
        animation: float 3s ease-in-out infinite;
    }
    
    .welcome-logo {
        animation: bounce 2s ease-in-out infinite;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        transition: transform 0.3s ease;
    }
    
    .welcome-logo:hover {
        transform: scale(1.1) rotate(5deg);
    }
    
    .welcome-logo-text {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: bold;
        animation: bounce 2s ease-in-out infinite;
        transition: transform 0.3s ease;
    }
    
    .welcome-logo-text:hover {
        transform: scale(1.1) rotate(5deg);
    }
    
    .waving-hand {
        display: inline-block;
        animation: wave 1.5s ease-in-out infinite;
        transform-origin: 70% 70%;
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .waving-hand:hover {
        animation: wave-fast 0.5s ease-in-out infinite;
        transform: scale(1.2);
    }
    
    @keyframes wave {
        0%, 100% {
            transform: rotate(0deg);
        }
        10%, 30% {
            transform: rotate(14deg);
        }
        20% {
            transform: rotate(-8deg);
        }
        40%, 60% {
            transform: rotate(10deg);
        }
        50% {
            transform: rotate(-5deg);
        }
    }
    
    @keyframes wave-fast {
        0%, 100% {
            transform: rotate(0deg) scale(1.2);
        }
        25% {
            transform: rotate(20deg) scale(1.2);
        }
        75% {
            transform: rotate(-20deg) scale(1.2);
        }
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }
    
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-8px);
        }
    }
    
    /* Interactive particles on hover */
    .welcome-logo-container::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .welcome-logo-container:hover::before {
        opacity: 1;
        animation: sparkle 1s ease-in-out infinite;
    }
    
    @keyframes sparkle {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 0.6;
        }
    }
    
    /* Coding-themed background animations */
    .code-float-1 {
        animation: codeFloat1 8s ease-in-out infinite;
    }
    
    .code-float-2 {
        animation: codeFloat2 10s ease-in-out infinite;
        animation-delay: 1s;
    }
    
    .code-float-3 {
        animation: codeFloat3 12s ease-in-out infinite;
        animation-delay: 2s;
    }
    
    .code-float-4 {
        animation: codeFloat4 9s ease-in-out infinite;
        animation-delay: 0.5s;
    }
    
    .code-float-5 {
        animation: codeFloat5 11s ease-in-out infinite;
        animation-delay: 1.5s;
    }
    
    .code-float-6 {
        animation: codeFloat6 13s ease-in-out infinite;
        animation-delay: 3s;
    }
    
    @keyframes codeFloat1 {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.05;
        }
        25% {
            transform: translate(20px, -15px) rotate(5deg);
            opacity: 0.08;
        }
        50% {
            transform: translate(-10px, -25px) rotate(-5deg);
            opacity: 0.05;
        }
        75% {
            transform: translate(15px, -10px) rotate(3deg);
            opacity: 0.07;
        }
    }
    
    @keyframes codeFloat2 {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.05;
        }
        33% {
            transform: translate(-15px, 20px) rotate(-8deg);
            opacity: 0.08;
        }
        66% {
            transform: translate(20px, 10px) rotate(8deg);
            opacity: 0.05;
        }
    }
    
    @keyframes codeFloat3 {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.05;
        }
        25% {
            transform: translate(25px, 15px) rotate(10deg);
            opacity: 0.07;
        }
        50% {
            transform: translate(-20px, 25px) rotate(-10deg);
            opacity: 0.05;
        }
        75% {
            transform: translate(10px, 20px) rotate(5deg);
            opacity: 0.06;
        }
    }
    
    @keyframes codeFloat4 {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.05;
        }
        50% {
            transform: translate(-15px, -20px) rotate(-7deg);
            opacity: 0.08;
        }
    }
    
    @keyframes codeFloat5 {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.05;
        }
        40% {
            transform: translate(30px, -20px) rotate(12deg);
            opacity: 0.07;
        }
        80% {
            transform: translate(-25px, 15px) rotate(-12deg);
            opacity: 0.05;
        }
    }
    
    @keyframes codeFloat6 {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
            opacity: 0.05;
        }
        30% {
            transform: translate(-20px, 30px) rotate(-15deg);
            opacity: 0.08;
        }
        60% {
            transform: translate(25px, -15px) rotate(15deg);
            opacity: 0.05;
        }
    }
</style>

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
    
    // Add interactive click effect to waving hand
    document.addEventListener('DOMContentLoaded', function() {
        const wavingHand = document.querySelector('.waving-hand');
        if (wavingHand) {
            wavingHand.addEventListener('click', function() {
                this.style.animation = 'wave-fast 0.3s ease-in-out 3';
                setTimeout(() => {
                    this.style.animation = 'wave 1.5s ease-in-out infinite';
                }, 900);
            });
        }
    });

    // Weather Widget - Using backend API to avoid CORS issues
    async function fetchWeather() {
        try {
            const response = await fetch('{{ route("weather.get") }}?city=Lahore');
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
