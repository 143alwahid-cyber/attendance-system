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
        if (!Schema::hasTable('payrolls')) {
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->date('payroll_month'); // Year-month stored as date (first day of month)
                $table->decimal('gross_salary', 12, 2);
                $table->integer('working_days');
                $table->decimal('salary_per_day', 12, 2);
                $table->decimal('salary_per_minute', 12, 4);
                $table->decimal('total_deductions', 12, 2)->default(0);
                $table->decimal('late_deductions', 12, 2)->default(0);
                $table->decimal('absent_deductions', 12, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('overtime_minutes', 10, 2)->default(0);
                $table->decimal('overtime_amount', 12, 2)->default(0);
                $table->decimal('compensation', 12, 2)->default(0);
                $table->decimal('net_salary', 12, 2);
                $table->json('daily_details'); // Store daily attendance details
                $table->timestamps();
                
                // Ensure one payroll per employee per month
                $table->unique(['employee_id', 'payroll_month']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
