<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="mb-10 text-center">
            <div class="inline-flex items-center justify-center rounded-2xl bg-white/70 shadow-md px-5 py-3">
                <div class="mr-3">
                    <img
                        src="{{ asset('assets/Devno Only Logo.png') }}"
                        alt="DevnoSol Logo"
                        class="h-10 w-auto"
                    >
                </div>
                <div class="text-left">
                    <p class="text-xs tracking-wide text-gray-500 uppercase">Attendance System</p>
                    <p class="text-base font-semibold text-gray-800">Admin Portal</p>
                </div>
            </div>
        </div>

        <div class="bg-white/90 backdrop-blur shadow-xl rounded-2xl p-8 border border-gray-100">
            <h1 class="text-xl font-semibold text-gray-900 mb-2 text-center">Sign in to your account</h1>
            <p class="text-sm text-gray-500 mb-6 text-center">
                Use your administrator credentials to access the attendance dashboard.
            </p>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 border border-red-100 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.perform') }}" class="space-y-5" novalidate>
                @csrf

                <div class="space-y-1">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                        placeholder="admin@devnosol.com"
                    >
                </div>

                <div class="space-y-1">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        minlength="8"
                        autocomplete="current-password"
                        class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                        placeholder="Enter your password"
                    >
                </div>

                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:outline-none focus:ring-0"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember this device
                        </label>
                    </div>
                    <span class="text-xs text-gray-400">
                        Secure login • No password reset
                    </span>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent bg-indigo-600 py-2.5 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                    >
                        Sign in
                    </button>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} DevnoSol. All rights reserved.
        </p>
    </div>
</body>
</html>

