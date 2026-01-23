<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Employee Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/Devno Only Logo.png') }}">
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
                            <p class="text-indigo-200 text-xs">Employee Portal</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                    <a
                        href="{{ route('employee.dashboard') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('employee.dashboard') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a
                        href="{{ route('employee.payrolls') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('employee.payroll*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        My Payrolls
                    </a>

                    <a
                        href="{{ route('employee.leaves.index') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('employee.leaves*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        My Leaves
                    </a>

                    <a
                        href="{{ route('employee.profile') }}"
                        class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('employee.profile*') ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-700/50 hover:text-white' }}"
                    >
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        My Profile
                    </a>
                </nav>

                <!-- User Section -->
                <div class="px-4 py-4 border-t border-indigo-700">
                    <div class="flex items-center px-4 py-3 mb-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center">
                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr(auth('employee')->user()->name, 0, 1)) }}</span>
                            </div>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-white">{{ auth('employee')->user()->name }}</p>
                            <p class="text-xs text-indigo-200">{{ 'DEVNO-' . auth('employee')->user()->employee_id }}</p>
                        </div>
                    </div>
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
            <!-- Top Bar -->
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
</body>
</html>
