<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'employee_id')) {
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('payrolls', 'payroll_month')) {
                $table->date('payroll_month');
            }
            if (!Schema::hasColumn('payrolls', 'gross_salary')) {
                $table->decimal('gross_salary', 12, 2);
            }
            if (!Schema::hasColumn('payrolls', 'working_days')) {
                $table->integer('working_days');
            }
            if (!Schema::hasColumn('payrolls', 'salary_per_day')) {
                $table->decimal('salary_per_day', 12, 2);
            }
            if (!Schema::hasColumn('payrolls', 'salary_per_minute')) {
                $table->decimal('salary_per_minute', 12, 4);
            }
            if (!Schema::hasColumn('payrolls', 'total_deductions')) {
                $table->decimal('total_deductions', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'late_deductions')) {
                $table->decimal('late_deductions', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'absent_deductions')) {
                $table->decimal('absent_deductions', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'overtime_minutes')) {
                $table->decimal('overtime_minutes', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'overtime_amount')) {
                $table->decimal('overtime_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'compensation')) {
                $table->decimal('compensation', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'net_salary')) {
                $table->decimal('net_salary', 12, 2);
            }
            if (!Schema::hasColumn('payrolls', 'daily_details')) {
                $table->json('daily_details');
            }
        });

        // Add unique constraint if it doesn't exist
        if (Schema::hasTable('payrolls') && Schema::hasColumn('payrolls', 'employee_id') && Schema::hasColumn('payrolls', 'payroll_month')) {
            try {
                Schema::table('payrolls', function (Blueprint $table) {
                    $table->unique(['employee_id', 'payroll_month']);
                });
            } catch (\Exception $e) {
                // Constraint might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns in down() to preserve data
    }
};
