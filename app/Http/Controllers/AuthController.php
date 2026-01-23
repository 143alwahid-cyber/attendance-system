<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the admin login form.
     */
    public function showAdminLoginForm(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Display the employee login form.
     */
    public function showEmployeeLoginForm(): View
    {
        return view('auth.employee-login');
    }

    /**
     * Handle admin login.
     */
    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->onlyInput('email');
    }

    /**
     * Handle employee login.
     */
    public function employeeLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'employee_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $employeeIdInput = $request->input('employee_id');
        $password = $request->input('password');

        // Extract ID from DEVNO-{id} format
        if (preg_match('/^DEVNO-(.+)$/i', $employeeIdInput, $matches)) {
            $employeeIdValue = $matches[1]; // Keep as string to match employee_id column
        } else {
            // Allow just the employee_id value as well
            $employeeIdValue = $employeeIdInput;
        }

        // Look up by employee_id column (not the primary key id)
        $employee = Employee::where('employee_id', $employeeIdValue)->first();

        if (!$employee) {
            return back()
                ->withErrors([
                    'employee_id' => 'The provided credentials do not match our records.',
                ])
                ->onlyInput('employee_id');
        }

        // If employee has no password set, set default password
        if (empty($employee->password)) {
            $employee->password = Hash::make('Lifeatdevno@2026');
            $employee->login_identifier = 'DEVNO-' . $employee->employee_id;
            $employee->save();
            // Reload employee to get the actual stored password (after cast)
            $employee->refresh();
        }

        if (Hash::check($password, $employee->password)) {
            // Login as employee
            Auth::guard('employee')->login($employee, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended('/employee/dashboard');
        }

        return back()
            ->withErrors([
                'employee_id' => 'The provided credentials do not match our records.',
            ])
            ->onlyInput('employee_id');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Check which guard was logged in before logout
        $wasAdmin = Auth::guard('web')->check();
        $wasEmployee = Auth::guard('employee')->check();

        // Logout from both guards
        Auth::guard('web')->logout();
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on which guard was logged in
        if ($wasAdmin) {
            return redirect()->route('admin.login');
        }
        return redirect()->route('employee.login');
    }
}

