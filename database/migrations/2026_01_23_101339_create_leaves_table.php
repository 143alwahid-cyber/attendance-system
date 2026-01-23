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
            // For PostgreSQL (Supabase), use raw SQL to ensure it works
            if (!Schema::hasTable('leaves')) {
                DB::statement('
                    CREATE TABLE leaves (
                        id BIGSERIAL PRIMARY KEY,
                        employee_id BIGINT NOT NULL,
                        leave_type VARCHAR(20) NOT NULL DEFAULT \'full_day\' CHECK (leave_type IN (\'half_day\', \'full_day\')),
                        leave_date DATE NOT NULL,
                        leave_format VARCHAR(20) NOT NULL DEFAULT \'casual\' CHECK (leave_format IN (\'casual\', \'medical\', \'annual\')),
                        description TEXT,
                        status VARCHAR(20) NOT NULL DEFAULT \'pending\' CHECK (status IN (\'pending\', \'approved\', \'rejected\')),
                        number_of_days DECIMAL(3, 1) NOT NULL DEFAULT 1.0,
                        rejection_reason TEXT,
                        approved_at TIMESTAMP NULL,
                        rejected_at TIMESTAMP NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        CONSTRAINT leaves_employee_id_foreign 
                            FOREIGN KEY (employee_id) 
                            REFERENCES employees(id) 
                            ON DELETE CASCADE
                    )
                ');
                
                // Create indexes
                DB::statement('CREATE INDEX leaves_employee_id_status_idx ON leaves(employee_id, status)');
                DB::statement('CREATE INDEX leaves_leave_date_idx ON leaves(leave_date)');
            }
        } else {
            // For other databases, use Schema builder
            if (!Schema::hasTable('leaves')) {
                Schema::create('leaves', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('employee_id');
                    $table->enum('leave_type', ['half_day', 'full_day'])->default('full_day');
                    $table->date('leave_date');
                    $table->enum('leave_format', ['casual', 'medical', 'annual'])->default('casual');
                    $table->text('description')->nullable();
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                    $table->decimal('number_of_days', 3, 1)->default(1.0);
                    $table->text('rejection_reason')->nullable();
                    $table->timestamp('approved_at')->nullable();
                    $table->timestamp('rejected_at')->nullable();
                    $table->timestamps();
                    
                    $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
                    $table->index(['employee_id', 'status']);
                    $table->index('leave_date');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
