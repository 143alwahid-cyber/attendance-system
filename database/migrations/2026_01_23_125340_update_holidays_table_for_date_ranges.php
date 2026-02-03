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
            // Add start_date and end_date columns
            DB::statement('ALTER TABLE holidays ADD COLUMN IF NOT EXISTS start_date DATE');
            DB::statement('ALTER TABLE holidays ADD COLUMN IF NOT EXISTS end_date DATE');
            
            // Migrate existing holiday_date to start_date and end_date
            DB::statement('UPDATE holidays SET start_date = holiday_date, end_date = holiday_date WHERE start_date IS NULL');
            
            // Make start_date and end_date NOT NULL after migration
            DB::statement('ALTER TABLE holidays ALTER COLUMN start_date SET NOT NULL');
            DB::statement('ALTER TABLE holidays ALTER COLUMN end_date SET NOT NULL');
            
            // Create indexes for date range queries
            DB::statement('CREATE INDEX IF NOT EXISTS holidays_start_date_idx ON holidays(start_date)');
            DB::statement('CREATE INDEX IF NOT EXISTS holidays_end_date_idx ON holidays(end_date)');
        } else {
            Schema::table('holidays', function (Blueprint $table) {
                $table->date('start_date')->nullable()->after('holiday_date');
                $table->date('end_date')->nullable()->after('start_date');
            });
            
            // Migrate existing data
            DB::statement('UPDATE holidays SET start_date = holiday_date, end_date = holiday_date WHERE start_date IS NULL');
            
            Schema::table('holidays', function (Blueprint $table) {
                $table->date('start_date')->nullable(false)->change();
                $table->date('end_date')->nullable(false)->change();
            });
            
            Schema::table('holidays', function (Blueprint $table) {
                $table->index('start_date');
                $table->index('end_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS holidays_start_date_idx');
            DB::statement('DROP INDEX IF EXISTS holidays_end_date_idx');
            DB::statement('ALTER TABLE holidays DROP COLUMN IF EXISTS start_date');
            DB::statement('ALTER TABLE holidays DROP COLUMN IF EXISTS end_date');
        } else {
            Schema::table('holidays', function (Blueprint $table) {
                $table->dropIndex(['start_date']);
                $table->dropIndex(['end_date']);
                $table->dropColumn(['start_date', 'end_date']);
            });
        }
    }
};
