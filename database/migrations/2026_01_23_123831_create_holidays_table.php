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
            DB::statement('
                CREATE TABLE holidays (
                    id BIGSERIAL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    holiday_date DATE NOT NULL,
                    description TEXT,
                    is_recurring BOOLEAN DEFAULT false,
                    year INTEGER,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL
                )
            ');
            
            // Create index for faster queries
            DB::statement('CREATE INDEX holidays_holiday_date_idx ON holidays(holiday_date)');
            DB::statement('CREATE INDEX holidays_year_idx ON holidays(year)');
        } else {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('holiday_date');
                $table->text('description')->nullable();
                $table->boolean('is_recurring')->default(false);
                $table->integer('year')->nullable(); // For recurring holidays, store the year
                $table->timestamps();
                
                $table->index('holiday_date');
                $table->index('year');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
