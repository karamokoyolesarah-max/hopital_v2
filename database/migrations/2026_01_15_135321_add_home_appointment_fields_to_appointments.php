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
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'home_address')) {
                $table->text('home_address')->nullable()->after('reason');
            }
            if (!Schema::hasColumn('appointments', 'patient_latitude')) {
                $table->decimal('patient_latitude', 10, 8)->nullable()->after('home_address');
            }
            if (!Schema::hasColumn('appointments', 'patient_longitude')) {
                $table->decimal('patient_longitude', 11, 8)->nullable()->after('patient_latitude');
            }
            if (!Schema::hasColumn('appointments', 'is_home_appointment')) {
                $table->boolean('is_home_appointment')->default(false)->after('patient_longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'home_address')) {
                $table->dropColumn('home_address');
            }
            if (Schema::hasColumn('appointments', 'patient_latitude')) {
                $table->dropColumn('patient_latitude');
            }
            if (Schema::hasColumn('appointments', 'patient_longitude')) {
                $table->dropColumn('patient_longitude');
            }
            if (Schema::hasColumn('appointments', 'is_home_appointment')) {
                $table->dropColumn('is_home_appointment');
            }
        });
    }
};
