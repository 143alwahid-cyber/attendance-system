<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $employees = Employee::query()
            ->orderByDesc('id')
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'employee_id' => ['required', 'string', 'min:1', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:employees,employee_id'],
            'salary' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        Employee::create($data);

        return redirect()
            ->route('employees.index')
            ->with('status', 'Employee created successfully.');
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'employee_id' => [
                'required',
                'string',
                'min:1',
                'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('employees', 'employee_id')->ignore($employee->id),
            ],
            'salary' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $employee->update($data);

        return redirect()
            ->route('employees.index')
            ->with('status', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('status', 'Employee deleted successfully.');
    }
}
