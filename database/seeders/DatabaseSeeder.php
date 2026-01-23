<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create/Update admin user
        User::query()->updateOrCreate(
            ['email' => 'admin@devnosol.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('DforDevno@2026'),
            ],
        );

        // Set default password for all existing employees
        $defaultPassword = Hash::make('Lifeatdevno@2026');
        Employee::whereNull('password')->orWhere('password', '')->chunkById(100, function ($employees) use ($defaultPassword) {
            foreach ($employees as $employee) {
                $employee->password = $defaultPassword;
                $employee->login_identifier = 'DEVNO-' . $employee->employee_id;
                $employee->save();
            }
        });
    }
}
