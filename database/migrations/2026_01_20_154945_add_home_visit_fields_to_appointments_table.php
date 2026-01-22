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
            if (!Schema::hasColumn('appointments', 'consultation_type')) {
                $table->enum('consultation_type', ['clinic', 'home', 'telemedicine'])->default('clinic')->after('status');
            }
            if (!Schema::hasColumn('appointments', 'home_visit_status')) {
                $table->enum('home_visit_status', ['requested', 'doctor_assigned', 'on_the_way', 'in_progress', 'completed', 'cancelled'])->nullable()->after('consultation_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            //
        });
    }
};
