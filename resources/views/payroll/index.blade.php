@extends('layouts.app')

@section('title', 'Generate Payroll')
@section('page-title', 'Generate Payroll')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Generate Payroll</h1>
        <p class="mt-1 text-sm text-gray-500">Select an employee and month to generate payroll with deductions.</p>
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
        <form method="POST" action="{{ route('payroll.generate') }}" class="space-y-5">
            @csrf

            <div class="space-y-1">
                <label for="employee_id" class="block text-sm font-medium text-gray-700">Select Employee</label>
                <select
                    id="employee_id"
                    name="employee_id"
                    required
                    class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                >
                    <option value="">-- Select an employee --</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->employee_id }}) - {{ number_format($emp->salary, 2) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label for="month" class="block text-sm font-medium text-gray-700">Select Month</label>
                <input
                    id="month"
                    name="month"
                    type="month"
                    value="{{ date('Y-m') }}"
                    required
                    class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                >
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="space-y-1">
                    <label for="overtime_minutes" class="block text-sm font-medium text-gray-700">Overtime (Minutes)</label>
                    <input
                        id="overtime_minutes"
                        name="overtime_minutes"
                        type="number"
                        min="0"
                        step="1"
                        value="{{ old('overtime_minutes', 0) }}"
                        class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                        placeholder="0"
                    >
                    <p class="text-xs text-gray-500">Overtime is calculated at 1.5× base salary per minute</p>
                </div>

                <div class="space-y-1">
                    <label for="compensation" class="block text-sm font-medium text-gray-700">Compensation Amount</label>
                    <input
                        id="compensation"
                        name="compensation"
                        type="number"
                        min="0"
                        step="0.01"
                        value="{{ old('compensation', 0) }}"
                        class="mt-1 block w-full rounded-xl border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-0 focus:border-gray-300 transition-colors"
                        placeholder="0.00"
                    >
                    <p class="text-xs text-gray-500">Compensation is added directly to net salary</p>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a
                    href="{{ route('dashboard') }}"
                    class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-200"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none"
                >
                    Generate Payroll
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
