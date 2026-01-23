<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Attendance System')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-indigo-900 to-indigo-800 shadow-xl">
            <div class="flex flex-col h-full">
                <!-- Logo Section -->
                <div class="flex items-center justify-center px-6 py-6 border-b border-indigo-700">
                    <div class="flex items-center space-x-3">
                        <img
                            src="{{ asset('assets/Devno Only Logo.png') }}"
                            alt="DevnoSol Logo"
                            class="h-10 w-auto"
                        >
                        <div>
                            <h1 class="text-white font-bold text-lg">DevnoSol</h1>
                            <p class="text-indigo-200 text-xs">Attendance System</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a
                        href="{{ route('employees.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('employees.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Employees
                    </a>

                    <a
                        href="{{ route('attendance.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('attendance.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Attendance
                    </a>

                    <a
                        href="{{ route('admin.leaves.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.leaves.*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Leave Management
                    </a>

                    @php
                        $isPayrollActive = request()->routeIs('payroll.*');
                        $isGenerateActive = request()->routeIs('payroll.index') || request()->routeIs('payroll.generate') || request()->routeIs('payroll.show');
                        $isSavedActive = request()->routeIs('payroll.saved') || request()->routeIs('payroll.view-saved') || request()->routeIs('payroll.download-saved');
                    @endphp
                    <div class="space-y-1">
                        <button
                            type="button"
                            onclick="togglePayrollMenu()"
                            class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ $isPayrollActive ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                        >
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Payroll
                            </div>
                            <svg id="payroll-menu-icon" class="w-4 h-4 transition-transform {{ $isPayrollActive ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div id="payroll-submenu" class="pl-4 space-y-1 {{ $isPayrollActive ? '' : 'hidden' }}">
                            <a
                                href="{{ route('payroll.index') }}"
                                class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $isGenerateActive ? 'bg-indigo-600 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                            >
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Generate Payroll
                            </a>
                            <a
                                href="{{ route('payroll.saved') }}"
                                class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $isSavedActive ? 'bg-indigo-600 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                            >
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Saved Payrolls
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- User Section -->
                <div class="px-4 py-4 border-t border-indigo-700">
                    @if(auth('web')->check())
                        <div class="flex items-center px-4 py-3 mb-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr(auth('web')->user()->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-white">{{ auth('web')->user()->name }}</p>
                                <p class="text-xs text-indigo-200">{{ auth('web')->user()->email }}</p>
                            </div>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-indigo-200 bg-indigo-700/50 rounded-lg hover:bg-indigo-700 hover:text-white transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar (optional, can be removed if not needed) -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        function togglePayrollMenu() {
            const submenu = document.getElementById('payroll-submenu');
            const icon = document.getElementById('payroll-menu-icon');
            
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.classList.add('rotate-90');
            } else {
                submenu.classList.add('hidden');
                icon.classList.remove('rotate-90');
            }
        }

        // Auto-expand payroll menu if on payroll page
        @if($isPayrollActive)
            document.addEventListener('DOMContentLoaded', function() {
                const submenu = document.getElementById('payroll-submenu');
                const icon = document.getElementById('payroll-menu-icon');
                if (submenu && submenu.classList.contains('hidden')) {
                    submenu.classList.remove('hidden');
                    icon.classList.add('rotate-90');
                }
            });
        @endif
    </script>
</body>
</html>
