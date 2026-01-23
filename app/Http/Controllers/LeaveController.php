<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeaveController extends Controller
{
    /**
     * Display a listing of the employee's leaves.
     */
    public function index(Request $request): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get filter parameters
        $statusFilter = $request->input('status', 'all');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $leaveFormatFilter = $request->input('leave_format', 'all');
        $sortOrder = $request->input('sort', 'desc');

        // Build query
        $query = Leave::where('employee_id', $employee->id)
            ->with('employee')
            ->orderBy('leave_date', $sortOrder)
            ->orderBy('created_at', $sortOrder);

        // Apply filters
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($leaveFormatFilter !== 'all') {
            $query->where('leave_format', $leaveFormatFilter);
        }

        if ($dateFrom) {
            $query->where('leave_date', '>=', Carbon::parse($dateFrom));
        }

        if ($dateTo) {
            $query->where('leave_date', '<=', Carbon::parse($dateTo));
        }

        $leaves = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Leave::where('employee_id', $employee->id)->count(),
            'pending' => Leave::where('employee_id', $employee->id)->where('status', 'pending')->count(),
            'approved' => Leave::where('employee_id', $employee->id)->where('status', 'approved')->count(),
            'rejected' => Leave::where('employee_id', $employee->id)->where('status', 'rejected')->count(),
            'total_days' => Leave::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->sum('number_of_days'),
        ];

        return view('employee.leaves.index', compact('leaves', 'stats', 'statusFilter', 'dateFrom', 'dateTo', 'leaveFormatFilter', 'sortOrder'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create(): View
    {
        $employee = Auth::guard('employee')->user();
        return view('employee.leaves.create', compact('employee'));
    }

    /**
     * Store a newly created leave request.
     */
    public function store(Request $request): RedirectResponse
    {
        $employee = Auth::guard('employee')->user();

        $validated = $request->validate([
            'leave_type' => ['required', 'in:half_day,full_day'],
            'leave_date' => ['required', 'date', 'after_or_equal:today'],
            'leave_format' => ['required', 'in:casual,medical,annual'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if leave already exists for this date
        $existingLeave = Leave::where('employee_id', $employee->id)
            ->where('leave_date', $validated['leave_date'])
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingLeave) {
            return redirect()
                ->route('employee.leaves.create')
                ->withInput()
                ->with('error', 'You already have a leave request for this date.');
        }

        // Calculate number of days
        $numberOfDays = $validated['leave_type'] === 'half_day' ? 0.5 : 1.0;

        Leave::create([
            'employee_id' => $employee->id,
            'leave_type' => $validated['leave_type'],
            'leave_date' => $validated['leave_date'],
            'leave_format' => $validated['leave_format'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
            'number_of_days' => $numberOfDays,
        ]);

        return redirect()
            ->route('employee.leaves.index')
            ->with('success', 'Leave request submitted successfully!');
    }
}
