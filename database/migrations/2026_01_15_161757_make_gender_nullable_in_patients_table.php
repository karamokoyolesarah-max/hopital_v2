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
        // First, update any invalid gender values to null or a valid value
        DB::table('patients')->whereNotIn('gender', ['Homme', 'Femme'])->update(['gender' => null]);

        Schema::table('patients', function (Blueprint $table) {
            $table->enum('gender', ['Homme', 'Femme', 'Other'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set default value for null genders before making it not nullable
        DB::table('patients')->whereNull('gender')->update(['gender' => 'Homme']);

        Schema::table('patients', function (Blueprint $table) {
            $table->enum('gender', ['Homme', 'Femme', 'Other'])->change();
        });
    }
};
