@extends('layouts.app')

@section('title', 'Upload Attendance')
@section('page-title', 'Upload Attendance')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Upload Attendance File</h1>
        <p class="mt-1 text-sm text-gray-500">
            Upload your attendance file (CSV). The system will parse rows by Employee ID (No. column), 
            Status (checkin / checkout), and Date/Time, then let you review and save them into the database.
        </p>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('attendance.preview') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="space-y-1">
                <label for="file" class="block text-sm font-medium text-gray-700">Attendance File (CSV)</label>
                <input
                    id="file"
                    name="file"
                    type="file"
                    accept=".csv,.pdf,text/csv,application/pdf"
                    required
                    class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100"
                >
                <p class="text-xs text-gray-500 mt-1">
                    
                </p>
            </div>

            <div class="flex items-center justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none"
                >
                    Upload &amp; Preview
                </button>
            </div>
        </form>
    </div>

    @if (!empty($records))
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Parsed Attendance Records</h2>
                    @if ($source_file)
                        <p class="text-xs text-gray-500 mt-1">Source file: {{ $source_file }}</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">Found {{ count($records) }} record(s)</p>
                </div>
                <form method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none"
                    >
                        Save to DB
                    </button>
                </form>
            </div>
            
            @if (isset($debug_text) && !empty($debug_text))
                <details class="mb-4">
                    <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-900">Show extracted PDF text (for debugging)</summary>
                    <pre class="mt-2 p-3 bg-gray-50 rounded text-xs overflow-auto max-h-40">{{ $debug_text }}</pre>
                </details>
            @endif
            
            @if (empty($records))
                <div class="rounded-lg border border-yellow-100 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                    No attendance records were found. Please check:
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>For CSV: Ensure the file has a header row with columns like "No.", "Employee ID", "Status", "Date", "Time"</li>
                        <li>For PDF: The file contains text (not just images) with columns for Employee ID (No.), Status (checkin/checkout), and Date/Time</li>
                        <li>Status values must be "checkin" or "checkout" (case-insensitive)</li>
                        @if (isset($debug_text) && !empty($debug_text))
                            <li>Check the debug text above to see what was extracted</li>
                        @endif
                    </ul>
                </div>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Date / Time</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Matched</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($records as $record)
                            <tr>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-gray-900">
                                        {{ $record['employee_name'] ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500 font-mono">
                                        ID: {{ $record['employee_identifier'] }}
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $record['status'] === 'checkin' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">
                                            {{ ucfirst($record['status']) }}
                                        </span>
                                        @if (isset($record['is_late']) && $record['is_late'])
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-red-50 text-red-700">
                                                Late
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-gray-800">
                                    @if (isset($record['occurred_at_formatted']))
                                        {{ $record['occurred_at_formatted'] }}
                                    @else
                                        {{ date('M d, Y g:i A', strtotime($record['occurred_at'])) }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-gray-700">
                                    @if ($record['employee_id'])
                                        <span class="text-xs text-green-600 font-medium">✓ Linked</span>
                                    @else
                                        <span class="text-xs text-red-500">✗ No match</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    @endif
</div>
@endsection
