<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class HolidayController extends Controller
{
    /**
     * Display a listing of holidays.
     */
    public function index(Request $request): View
    {
        $yearFilter = $request->input('year', date('Y'));
        $sortOrder = $request->input('sort', 'asc');

        $query = Holiday::orderBy('holiday_date', $sortOrder);

        if ($yearFilter) {
            $query->where(function ($q) use ($yearFilter) {
                $q->where('year', $yearFilter)
                  ->orWhere('is_recurring', true)
                  ->orWhereNull('year');
            });
        }

        $holidays = $query->paginate(20)->withQueryString();

        // Get all unique years from holidays
        $years = Holiday::selectRaw('COALESCE(year, EXTRACT(YEAR FROM start_date)::integer) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            $years = [date('Y'), date('Y') + 1];
        }

        return view('admin.holidays.index', compact('holidays', 'yearFilter', 'sortOrder', 'years'));
    }

    /**
     * Show the form for creating a new holiday.
     */
    public function create(): View
    {
        return view('admin.holidays.create');
    }

    /**
     * Store a newly created holiday.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_recurring' => ['boolean'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        
        Holiday::create([
            'name' => $validated['name'],
            'holiday_date' => $startDate, // Keep for backward compatibility
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => $validated['description'] ?? null,
            'is_recurring' => $request->has('is_recurring'),
            'year' => $request->has('is_recurring') ? null : $startDate->year,
        ]);

        return redirect()
            ->route('admin.holidays.index')
            ->with('success', 'Holiday created successfully!');
    }

    /**
     * Show the form for editing the specified holiday.
     */
    public function edit(Holiday $holiday): View
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    /**
     * Update the specified holiday.
     */
    public function update(Request $request, Holiday $holiday): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_recurring' => ['boolean'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $holiday->update([
            'name' => $validated['name'],
            'holiday_date' => $startDate, // Keep for backward compatibility
            'start_date' => $startDate,
            'end_date' => $endDate,
            'description' => $validated['description'] ?? null,
            'is_recurring' => $request->has('is_recurring'),
            'year' => $request->has('is_recurring') ? null : $startDate->year,
        ]);

        return redirect()
            ->route('admin.holidays.index')
            ->with('success', 'Holiday updated successfully!');
    }

    /**
     * Remove the specified holiday.
     */
    public function destroy(Holiday $holiday): RedirectResponse
    {
        $holiday->delete();

        return redirect()
            ->route('admin.holidays.index')
            ->with('success', 'Holiday deleted successfully!');
    }

    /**
     * Display holidays for employees (read-only view).
     */
    public function employeeIndex(Request $request): View
    {
        $yearFilter = $request->input('year', date('Y'));

        $query = Holiday::where(function ($q) use ($yearFilter) {
            // One-time holidays in the selected year
            $q->whereYear('start_date', $yearFilter)
              ->where('is_recurring', false);
        })
        ->orWhere(function ($q) {
            // Recurring holidays (apply to all years)
            $q->where('is_recurring', true);
        })
        ->orderByRaw('EXTRACT(MONTH FROM start_date), EXTRACT(DAY FROM start_date)');

        $holidays = $query->get()->map(function ($holiday) use ($yearFilter) {
            // For recurring holidays, set the dates to the current year
            if ($holiday->is_recurring) {
                $holiday->display_start_date = Carbon::create($yearFilter, $holiday->start_date->month, $holiday->start_date->day);
                $holiday->display_end_date = Carbon::create($yearFilter, $holiday->end_date->month, $holiday->end_date->day);
            } else {
                $holiday->display_start_date = $holiday->start_date;
                $holiday->display_end_date = $holiday->end_date;
            }
            return $holiday;
        })->sortBy('display_start_date');

        // Get all unique years
        $years = Holiday::selectRaw('COALESCE(year, EXTRACT(YEAR FROM start_date)::integer) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            $years = [date('Y'), date('Y') + 1];
        }

        return view('employee.holidays.index', compact('holidays', 'yearFilter', 'years'));
    }
}
