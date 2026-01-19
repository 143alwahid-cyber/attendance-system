@extends('layouts.app')

@section('title', 'Create Employee')
@section('page-title', 'New Employee')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">New Employee</h1>
        <p class="mt-1 text-sm text-gray-500">Add a new employee record.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('employees.store') }}" class="space-y-5" novalidate>
            @csrf

            <div class="space-y-1">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                    placeholder="e.g. John Smith"
                >
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="space-y-1">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                    <input
                        id="employee_id"
                        name="employee_id"
                        type="text"
                        value="{{ old('employee_id') }}"
                        required
                        class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                        placeholder="e.g. EMP-001"
                    >
                    <p class="text-xs text-gray-500">Use letters, numbers, dot, underscore, or hyphen.</p>
                </div>

                <div class="space-y-1">
                    <label for="salary" class="block text-sm font-medium text-gray-700">Salary</label>
                    <input
                        id="salary"
                        name="salary"
                        type="number"
                        step="0.01"
                        min="0"
                        value="{{ old('salary') }}"
                        required
                        class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                        placeholder="e.g. 50000.00"
                    >
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a
                    href="{{ route('employees.index') }}"
                    class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-200"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none"
                >
                    Create Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
