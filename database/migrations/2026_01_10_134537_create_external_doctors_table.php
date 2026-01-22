<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('external_doctors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->string('speciality'); // Spécialité du médecin
            $table->string('license_number')->unique(); // Numéro de licence/certificat
            $table->text('qualifications')->nullable(); // Diplômes et qualifications
            $table->text('bio')->nullable(); // Biographie
            $table->string('profile_photo')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->json('availability')->nullable(); // Disponibilités (format JSON)
            $table->decimal('consultation_fee', 10, 2)->nullable(); // Tarif consultation
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_doctors');
    }
};