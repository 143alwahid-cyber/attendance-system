<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Smalot\PdfParser\Parser;

class AttendanceController extends Controller
{
    public function uploadForm(): View
    {
        $preview = Session::get('attendance_preview');

        return view('attendance.upload', [
            'records' => $preview['records'] ?? [],
            'source_file' => $preview['source_file'] ?? null,
            'debug_text' => $preview['raw_text'] ?? null,
        ]);
    }

    public function preview(Request $request, Parser $parser): View
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,csv,txt', 'max:20480'],
        ]);

        $file = $data['file'];
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Detect file type and parse accordingly
        if ($extension === 'csv' || $mimeType === 'text/csv' || $mimeType === 'application/csv') {
            return $this->parseCsv($file);
        } else {
            return $this->parsePdf($file, $parser);
        }
    }
    
    private function parseCsv($file): View
    {
        try {
            $records = [];
            $handle = fopen($file->getRealPath(), 'r');
            
            if ($handle === false) {
                throw new \Exception('Could not open CSV file.');
            }
            
            $headerRow = null;
            $noColumnIndex = null;
            $nameColumnIndex = null;
            $statusColumnIndex = null;
            $dateColumnIndex = null;
            $timeColumnIndex = null;
            $rowNum = 0;
            
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Detect header row
                if ($headerRow === null) {
                    $headerRow = $row;
                    // Find column indices - look for exact matches first
                    foreach ($row as $idx => $header) {
                        $headerClean = trim($header, '"');
                        $headerLower = strtolower($headerClean);
                        
                        // Look for "No." column (with period) - this is the Employee ID
                        if ($headerClean === 'No.' || stripos($headerLower, 'no.') !== false) {
                            $noColumnIndex = $idx;
                        }
                        // Look for "Name" column - Employee Name
                        if ($headerClean === 'Name' || stripos($headerLower, 'name') !== false) {
                            $nameColumnIndex = $idx;
                        }
                        // Look for "Status" column
                        if ($headerClean === 'Status' || stripos($headerLower, 'status') !== false) {
                            $statusColumnIndex = $idx;
                        }
                        // Look for "Date/Time" or combined date/time column
                        if (stripos($headerClean, 'Date/Time') !== false || 
                            stripos($headerClean, 'DateTime') !== false ||
                            (stripos($headerLower, 'date') !== false && stripos($headerLower, 'time') !== false)) {
                            $dateColumnIndex = $idx;
                        } elseif (stripos($headerLower, 'date') !== false && $dateColumnIndex === null) {
                            $dateColumnIndex = $idx;
                        } elseif (stripos($headerLower, 'time') !== false && $timeColumnIndex === null) {
                            $timeColumnIndex = $idx;
                        }
                    }
                    continue;
                }
                
                // Parse data rows
                $employeeIdentifier = null;
                $employeeName = null;
                $statusRaw = null;
                $dateTimeString = null;
                
                // Get Employee ID from No. column
                if ($noColumnIndex !== null && isset($row[$noColumnIndex])) {
                    $employeeIdentifier = trim($row[$noColumnIndex], '"');
                }
                
                // Get Employee Name from Name column
                if ($nameColumnIndex !== null && isset($row[$nameColumnIndex])) {
                    $employeeName = trim($row[$nameColumnIndex], '"');
                }
                
                // Get Status from Status column
                if ($statusColumnIndex !== null && isset($row[$statusColumnIndex])) {
                    $statusRaw = trim($row[$statusColumnIndex], '"');
                }
                
                // Get Date/Time from Date/Time column (combined)
                if ($dateColumnIndex !== null && isset($row[$dateColumnIndex])) {
                    $dateTimeString = trim($row[$dateColumnIndex], '"');
                    // If we have separate time column, append it
                    if ($timeColumnIndex !== null && isset($row[$timeColumnIndex])) {
                        $timeValue = trim($row[$timeColumnIndex], '"');
                        if (!empty($timeValue)) {
                            $dateTimeString .= ' ' . $timeValue;
                        }
                    }
                }
                
                // Validate required fields
                if (!$employeeIdentifier || !$statusRaw || !$dateTimeString) {
                    continue;
                }
                
                // Normalize status - handle "C/In" and "C/Out" format
                $statusRaw = trim($statusRaw);
                $statusLower = strtolower($statusRaw);
                
                // Handle "C/In", "C/Out", "Check In", "Check Out", etc.
                if (stripos($statusLower, 'in') !== false && stripos($statusLower, 'out') === false) {
                    $statusRaw = 'checkin';
                } elseif (stripos($statusLower, 'out') !== false) {
                    $statusRaw = 'checkout';
                } else {
                    // Try other variations
                    $statusLower = str_replace(['/', '-', '_', ' '], '', $statusLower);
                    if ($statusLower === 'in' || $statusLower === 'checkin' || $statusLower === 'cin') {
                        $statusRaw = 'checkin';
                    } elseif ($statusLower === 'out' || $statusLower === 'checkout' || $statusLower === 'cout') {
                        $statusRaw = 'checkout';
                    } else {
                        continue; // Invalid status
                    }
                }
                
                // Parse date/time
                $occurredAt = $this->parseDateTime($dateTimeString);
                if (!$occurredAt) {
                    continue;
                }
                
                // Find matching employee
                $employee = Employee::where('employee_id', $employeeIdentifier)->first();
                
                // Check if checkin is late (after 10:00 AM)
                $isLate = false;
                if ($statusRaw === 'checkin') {
                    $checkinTime = $occurredAt->format('H:i');
                    // Check if time is after 10:00 AM (10:00 in 24-hour format)
                    if ($checkinTime > '10:00') {
                        $isLate = true;
                    }
                }
                
                $records[] = [
                    'employee_identifier' => $employeeIdentifier,
                    'employee_name' => $employeeName,
                    'employee_id' => $employee?->id,
                    'status' => $statusRaw,
                    'occurred_at' => $occurredAt->toDateTimeString(),
                    'occurred_at_formatted' => $occurredAt->format('M d, Y g:i A'), // 12-hour format
                    'is_late' => $isLate,
                    'raw' => implode(', ', $row),
                ];
            }
            
            fclose($handle);
            
            Session::put('attendance_preview', [
                'records' => $records,
                'source_file' => $file->getClientOriginalName(),
            ]);
            
            return view('attendance.upload', [
                'records' => $records,
                'source_file' => $file->getClientOriginalName(),
            ]);
            
        } catch (\Exception $e) {
            return redirect()
                ->route('attendance.upload')
                ->withErrors(['file' => 'Error parsing CSV: ' . $e->getMessage()]);
        }
    }
    
    private function parsePdf($file, Parser $parser): View
    {
        try {
            $pdf = $parser->parseFile($file->getRealPath());
            
            // Try multiple extraction methods
            $text = $pdf->getText();
            
            // If main text extraction is empty, try page-by-page
            if (empty(trim($text))) {
                $pages = $pdf->getPages();
                $text = '';
                foreach ($pages as $page) {
                    $pageText = $page->getText();
                    if (!empty(trim($pageText))) {
                        $text .= $pageText . "\n";
                    }
                }
            }
            
            // Store raw extracted text for debugging
            $rawText = $text;
            
            // Normalize line breaks and clean up
            $text = preg_replace('/\r\n|\r|\n/', "\n", $text);
            $lines = explode("\n", $text);
            
            $records = [];
            $headerFound = false;
            $noColumnIndex = null;
            $statusColumnIndex = null;
            $dateColumnIndex = null;
            
            foreach ($lines as $lineNum => $line) {
                $originalLine = $line;
                $line = trim($line);
                
                // Skip empty lines
                if ($line === '') {
                    continue;
                }
                
                // Detect header row to understand column structure
                if (!$headerFound && (
                    stripos($line, 'no') !== false || 
                    stripos($line, 'employee') !== false ||
                    stripos($line, 'status') !== false ||
                    stripos($line, 'date') !== false ||
                    stripos($line, 'time') !== false
                )) {
                    $headerFound = true;
                    // Try to identify column positions
                    $headerParts = preg_split('/\s{2,}|\t/', $line);
                    foreach ($headerParts as $idx => $headerPart) {
                        $headerPart = strtolower(trim($headerPart));
                        if (stripos($headerPart, 'no') !== false) {
                            $noColumnIndex = $idx;
                        }
                        if (stripos($headerPart, 'status') !== false || 
                            stripos($headerPart, 'check') !== false) {
                            $statusColumnIndex = $idx;
                        }
                        if (stripos($headerPart, 'date') !== false || 
                            stripos($headerPart, 'time') !== false) {
                            $dateColumnIndex = $idx;
                        }
                    }
                    continue;
                }
                
                // Skip if looks like header or separator
                if (stripos($line, 'no.') === 0 || 
                    stripos($line, 'employee') === 0 ||
                    preg_match('/^[-=]+$/', $line)) {
                    continue;
                }
                
                // Try tab-separated first (common in PDF tables)
                $parts = preg_split('/\s{2,}|\t/', $line);
                
                // If that doesn't work, try space-separated
                if (count($parts) < 3) {
                    $parts = preg_split('/\s+/', $line);
                }
                
                // Clean parts
                $parts = array_map('trim', $parts);
                $parts = array_filter($parts, function($p) { return $p !== ''; });
                $parts = array_values($parts);
                
                if (count($parts) < 3) {
                    continue;
                }
                
                // Try to identify columns
                $employeeIdentifier = null;
                $statusRaw = null;
                $dateTimeString = null;
                
                // Method 1: Use detected column indices if available
                if ($noColumnIndex !== null && isset($parts[$noColumnIndex])) {
                    $employeeIdentifier = $parts[$noColumnIndex];
                }
                if ($statusColumnIndex !== null && isset($parts[$statusColumnIndex])) {
                    $statusRaw = strtolower($parts[$statusColumnIndex]);
                }
                if ($dateColumnIndex !== null && isset($parts[$dateColumnIndex])) {
                    $dateTimeString = $parts[$dateColumnIndex];
                    if (isset($parts[$dateColumnIndex + 1])) {
                        $dateTimeString .= ' ' . $parts[$dateColumnIndex + 1];
                    }
                }
                
                // Method 2: Heuristic parsing if column detection didn't work
                if (!$employeeIdentifier || !$statusRaw || !$dateTimeString) {
                    // Look for status keywords
                    foreach ($parts as $idx => $part) {
                        $partLower = strtolower($part);
                        if (in_array($partLower, ['checkin', 'checkout', 'check-in', 'check-out', 'in', 'out'])) {
                            $statusRaw = $partLower === 'in' ? 'checkin' : ($partLower === 'out' ? 'checkout' : $partLower);
                            // Employee ID is likely before status
                            if ($idx > 0) {
                                $employeeIdentifier = $parts[$idx - 1];
                            }
                            // Date/Time is likely after status
                            if (isset($parts[$idx + 1])) {
                                $dateTimeString = $parts[$idx + 1];
                                if (isset($parts[$idx + 2])) {
                                    $dateTimeString .= ' ' . $parts[$idx + 2];
                                }
                            }
                            break;
                        }
                    }
                    
                    // If still not found, try first column as employee ID
                    if (!$employeeIdentifier && count($parts) > 0) {
                        $employeeIdentifier = $parts[0];
                    }
                    
                    // Look for date-like patterns in remaining parts
                    if (!$dateTimeString) {
                        foreach ($parts as $part) {
                            if (preg_match('/\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4}/', $part) || 
                                preg_match('/\d{4}[-\/]\d{1,2}[-\/]\d{1,2}/', $part)) {
                                $dateTimeString = $part;
                                // Check if next part is time
                                $nextIdx = array_search($part, $parts) + 1;
                                if (isset($parts[$nextIdx]) && preg_match('/\d{1,2}:\d{2}/', $parts[$nextIdx])) {
                                    $dateTimeString .= ' ' . $parts[$nextIdx];
                                }
                                break;
                            }
                        }
                    }
                }
                
                // Normalize status
                if ($statusRaw) {
                    $statusRaw = strtolower(str_replace(['-', '_'], '', $statusRaw));
                    if ($statusRaw === 'in' || $statusRaw === 'checkin' || $statusRaw === 'checkin') {
                        $statusRaw = 'checkin';
                    } elseif ($statusRaw === 'out' || $statusRaw === 'checkout' || $statusRaw === 'checkout') {
                        $statusRaw = 'checkout';
                    } else {
                        continue; // Invalid status
                    }
                } else {
                    continue; // No status found
                }
                
                if (!$employeeIdentifier || !$dateTimeString) {
                    continue;
                }
                
                // Parse date/time
                $occurredAt = $this->parseDateTime($dateTimeString);
                
                if (!$occurredAt) {
                    continue;
                }
                
                // Find matching employee
                $employee = Employee::where('employee_id', $employeeIdentifier)->first();
                
                // Check if checkin is late (after 10:00 AM)
                $isLate = false;
                if ($statusRaw === 'checkin') {
                    $checkinTime = $occurredAt->format('H:i');
                    // Check if time is after 10:00 AM (10:00 in 24-hour format)
                    if ($checkinTime > '10:00') {
                        $isLate = true;
                    }
                }
                
                $records[] = [
                    'employee_identifier' => $employeeIdentifier,
                    'employee_name' => $employee?->name ?? null,
                    'employee_id' => $employee?->id,
                    'status' => $statusRaw,
                    'occurred_at' => $occurredAt->toDateTimeString(),
                    'occurred_at_formatted' => $occurredAt->format('M d, Y g:i A'), // 12-hour format
                    'is_late' => $isLate,
                    'raw' => $originalLine,
                ];
            }
            
            Session::put('attendance_preview', [
                'records' => $records,
                'source_file' => $file->getClientOriginalName(),
                'raw_text' => substr($rawText, 0, 5000), // Store first 5KB for debugging
            ]);
            
            return view('attendance.upload', [
                'records' => $records,
                'source_file' => $file->getClientOriginalName(),
                'debug_text' => substr($rawText, 0, 2000), // Show first 2KB for debugging
            ]);
            
        } catch (\Exception $e) {
            return redirect()
                ->route('attendance.upload')
                ->withErrors(['file' => 'Error parsing PDF: ' . $e->getMessage()]);
        }
    }

    public function storeFromPreview(Request $request): RedirectResponse
    {
        $preview = Session::get('attendance_preview');

        if (! $preview || empty($preview['records'])) {
            return redirect()
                ->route('attendance.upload')
                ->with('status', 'No attendance data to save. Please upload a CSV or PDF file first.');
        }

        $records = $preview['records'];
        $sourceFile = $preview['source_file'] ?? null;

        $saved = 0;
        $skipped = 0;

        foreach ($records as $record) {
            if (! $record['employee_id']) {
                $skipped++;
                continue;
            }

            Attendance::create([
                'employee_id' => $record['employee_id'],
                'status' => $record['status'],
                'occurred_at' => Carbon::parse($record['occurred_at']),
                'source_file' => $sourceFile,
                'raw_payload' => $record,
            ]);
            
            $saved++;
        }

        Session::forget('attendance_preview');

        $message = "Saved {$saved} attendance record(s) to database.";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} record(s) with no matching employee.";
        }

        return redirect()
            ->route('attendance.upload')
            ->with('status', $message);
    }

    private function parseDateTime(string $input): ?Carbon
    {
        $input = trim($input);

        if ($input === '') {
            return null;
        }

        // Try formats with AM/PM first (common in CSV exports)
        $formats = [
            'm/d/Y g:i A',      // 12/12/2025 7:44 PM (12-hour with AM/PM, no leading zero on hour)
            'm/d/Y h:i A',      // 12/12/2025 07:44 PM (12-hour with AM/PM, with leading zero)
            'm/d/Y g:i:s A',    // 12/12/2025 7:44:30 PM (with seconds)
            'm/d/Y h:i:s A',    // 12/12/2025 07:44:30 PM (with seconds, leading zero)
            'd/m/Y g:i A',      // 12/12/2025 7:44 PM (DD/MM/YYYY format)
            'd/m/Y h:i A',
            'Y-m-d H:i',        // 2025-12-12 19:44 (24-hour format)
            'Y-m-d H:i:s',
            'd/m/Y H:i',
            'd/m/Y H:i:s',
            'd-m-Y H:i',
            'd-m-Y H:i:s',
            'm/d/Y H:i',        // 12/12/2025 19:44 (24-hour format)
            'm/d/Y H:i:s',
        ];

        foreach ($formats as $format) {
            try {
                $dt = Carbon::createFromFormat($format, $input);
                if ($dt !== false) {
                    return $dt;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        // Fallback to Carbon's intelligent parser
        try {
            return Carbon::parse($input);
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    public function index(Request $request): View
    {
        $query = Attendance::with('employee')->orderBy('occurred_at', 'desc');
        
        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }
        
        // Filter by source file
        if ($request->filled('source_file')) {
            $query->where('source_file', 'like', '%' . $request->source_file . '%');
        }
        
        // Get counts before pagination
        $totalCount = (clone $query)->count();
        $checkinCount = (clone $query)->where('status', 'checkin')->count();
        $checkoutCount = (clone $query)->where('status', 'checkout')->count();
        
        $attendances = $query->paginate(50)->withQueryString();
        $employees = Employee::orderBy('name')->get();
        
        // Get unique source files for filter
        $sourceFiles = Attendance::whereNotNull('source_file')
            ->distinct()
            ->pluck('source_file')
            ->filter()
            ->sort()
            ->values();
        
        return view('attendance.index', compact('attendances', 'employees', 'sourceFiles', 'totalCount', 'checkinCount', 'checkoutCount'));
    }
}
