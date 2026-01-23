@extends('layouts.employee')

@section('title', 'My Payrolls')
@section('page-title', 'My Payrolls')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Payrolls</h1>
        <p class="mt-1 text-sm text-gray-500">View and download your payroll statements</p>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
        <form method="GET" action="{{ route('employee.payrolls') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <input
                    type="month"
                    id="month"
                    name="month"
                    value="{{ request('month') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>

            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Month</label>
                <input
                    type="month"
                    id="date_from"
                    name="date_from"
                    value="{{ request('date_from') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>

            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Month</label>
                <input
                    type="month"
                    id="date_to"
                    name="date_to"
                    value="{{ request('date_to') }}"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>

            <div class="md:col-span-3 flex items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                >
                    Apply Filters
                </button>
                <a
                    href="{{ route('employee.payrolls') }}"
                    class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-200"
                >
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Payrolls Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-xl font-bold text-gray-900">Payroll Records</h2>
            <p class="text-sm text-gray-600 mt-1">Total: {{ $payrolls->total() }} payroll(s)</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Salary</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saved On</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payrolls as $payroll)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($payroll->payroll_month)->format('F Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $payroll->working_days }} working days</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($payroll->gross_salary, 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-medium text-red-600">-{{ number_format($payroll->total_deductions + $payroll->tax_amount, 2) }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($payroll->late_deductions > 0 || $payroll->absent_deductions > 0)
                                        Late: -{{ number_format($payroll->late_deductions, 2) }}, 
                                        Absent: -{{ number_format($payroll->absent_deductions, 2) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="text-sm font-bold text-green-600">{{ number_format($payroll->net_salary, 2) }}</div>
                                @if($payroll->overtime_amount > 0 || $payroll->compensation > 0)
                                    <div class="text-xs text-green-500">
                                        @if($payroll->overtime_amount > 0)
                                            Overtime: +{{ number_format($payroll->overtime_amount, 2) }}
                                        @endif
                                        @if($payroll->compensation > 0)
                                            Compensation: +{{ number_format($payroll->compensation, 2) }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $payroll->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $payroll->created_at->format('g:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a
                                    href="{{ route('employee.payroll.download', $payroll) }}"
                                    class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No payrolls found. Payrolls will appear here once they are generated and saved by the administrator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payrolls->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
