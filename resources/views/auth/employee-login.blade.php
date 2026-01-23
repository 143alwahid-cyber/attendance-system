<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login - DevnoSol</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(5deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.2); }
            50% { box-shadow: 0 0 30px rgba(99, 102, 241, 0.4); }
        }
        @keyframes subtle-shift {
            0%, 100% { transform: translateX(0) translateY(0); }
            33% { transform: translateX(30px) translateY(-30px); }
            66% { transform: translateX(-20px) translateY(20px); }
        }
        .floating { animation: float 8s ease-in-out infinite; }
        .glow { animation: pulse-glow 3s ease-in-out infinite; }
        .subtle-shift { animation: subtle-shift 20s ease-in-out infinite; }
        .bg-pattern {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 50%, #f0f4f8 100%);
            position: relative;
        }
        .bg-pattern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
            background-size: 100% 100%;
        }
        .geometric-shape {
            position: absolute;
            opacity: 0.1;
            border-radius: 20% 80% 30% 70% / 60% 40% 60% 40%;
        }
    </style>
</head>
<body class="min-h-screen bg-pattern flex items-center justify-center px-4 py-12">
    <!-- Subtle Geometric Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="geometric-shape w-96 h-96 bg-indigo-300 top-10 left-10 floating subtle-shift" style="animation-delay: 0s;"></div>
        <div class="geometric-shape w-80 h-80 bg-purple-200 bottom-20 right-20 floating subtle-shift" style="animation-delay: 3s;"></div>
        <div class="geometric-shape w-72 h-72 bg-blue-200 top-1/2 right-1/4 floating subtle-shift" style="animation-delay: 6s;"></div>
        <div class="geometric-shape w-64 h-64 bg-indigo-200 bottom-1/3 left-1/4 floating subtle-shift" style="animation-delay: 9s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo Section with Animation -->
        <div class="mb-10 text-center">
            <div class="inline-flex items-center justify-center rounded-2xl bg-white/90 backdrop-blur-md shadow-xl px-6 py-4 border border-indigo-100 glow">
                <div class="mr-4">
                    <img
                        src="{{ asset('assets/Devno Only Logo.png') }}"
                        alt="DevnoSol Logo"
                        class="h-12 w-auto"
                    >
                </div>
                <div class="text-left">
                    <p class="text-xs tracking-wider text-indigo-600 uppercase font-semibold">Employee Portal</p>
                    <p class="text-lg font-bold text-gray-800">DevnoSol</p>
                </div>
            </div>
        </div>

        <!-- Login Card -->
        <div class="bg-white/95 backdrop-blur-xl shadow-2xl rounded-3xl p-8 border border-indigo-100/50 relative overflow-hidden">
            <!-- Subtle Decorative Elements -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-full blur-3xl -mr-20 -mt-20 opacity-60"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-gradient-to-tr from-blue-50 to-indigo-50 rounded-full blur-2xl -ml-16 -mb-16 opacity-60"></div>
            
            <div class="relative z-10">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
                        Welcome Back!
                    </h1>
                    <p class="text-sm text-gray-600">
                        Sign in to view your attendance records
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 border-2 border-red-200 p-4 text-sm text-red-700 shadow-lg">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 rounded-xl bg-green-50 border-2 border-green-200 p-4 text-sm text-green-700 shadow-lg">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('employee.login.perform') }}" class="space-y-6" novalidate>
                    @csrf

                    <div class="space-y-2">
                        <label for="employee_id" class="block text-sm font-semibold text-gray-700">
                            Employee ID
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                            </div>
                            <input
                                id="employee_id"
                                name="employee_id"
                                type="text"
                                value="{{ old('employee_id') }}"
                                required
                                autocomplete="username"
                                class="block w-full pl-10 pr-3 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm hover:shadow-md"
                                placeholder="DEVNO-3 or 3"
                            >
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Enter your Employee ID in format: <span class="font-mono font-semibold">DEVNO-{your-id}</span>
                        </p>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-semibold text-gray-700">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                minlength="8"
                                autocomplete="current-password"
                                class="block w-full pl-10 pr-3 py-3 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm hover:shadow-md"
                                placeholder="Enter your password"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700 font-medium">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="w-full inline-flex justify-center items-center rounded-xl border border-transparent bg-gradient-to-r from-indigo-600 to-purple-600 py-3.5 px-6 text-sm font-semibold text-white shadow-lg hover:shadow-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-[1.02] active:scale-[0.98]"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Sign In
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('admin.login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                        ← Admin Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-center text-xs text-gray-500 font-medium">
            &copy; {{ date('Y') }} DevnoSol. All rights reserved.
        </p>
    </div>
</body>
</html>
