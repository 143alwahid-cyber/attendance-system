<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL: Use raw SQL to ensure it works
            DB::statement('DROP TABLE IF EXISTS payrolls CASCADE');
            
            DB::statement('
                CREATE TABLE payrolls (
                    id BIGSERIAL PRIMARY KEY,
                    employee_id BIGINT NOT NULL,
                    payroll_month DATE NOT NULL,
                    gross_salary DECIMAL(12, 2) NOT NULL,
                    working_days INTEGER NOT NULL,
                    salary_per_day DECIMAL(12, 2) NOT NULL,
                    salary_per_minute DECIMAL(12, 4) NOT NULL,
                    total_deductions DECIMAL(12, 2) DEFAULT 0,
                    late_deductions DECIMAL(12, 2) DEFAULT 0,
                    absent_deductions DECIMAL(12, 2) DEFAULT 0,
                    tax_amount DECIMAL(12, 2) DEFAULT 0,
                    overtime_minutes DECIMAL(10, 2) DEFAULT 0,
                    overtime_amount DECIMAL(12, 2) DEFAULT 0,
                    compensation DECIMAL(12, 2) DEFAULT 0,
                    net_salary DECIMAL(12, 2) NOT NULL,
                    daily_details JSONB NOT NULL,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    CONSTRAINT payrolls_employee_id_foreign 
                        FOREIGN KEY (employee_id) 
                        REFERENCES employees(id) 
                        ON DELETE CASCADE,
                    CONSTRAINT payrolls_employee_id_payroll_month_unique 
                        UNIQUE (employee_id, payroll_month)
                )
            ');
        } else {
            // For other databases (SQLite, MySQL), use Schema builder
            Schema::dropIfExists('payrolls');
            
            Schema::create('payrolls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->date('payroll_month');
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
                $table->json('daily_details');
                $table->timestamps();
                
                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
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
