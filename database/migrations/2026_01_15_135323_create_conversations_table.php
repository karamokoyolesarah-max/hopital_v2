<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop existing tables if they exist (from previous migrations or partial runs)
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');

        // Table des conversations
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('external_doctor_id')->nullable()->constrained('external_doctors')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->timestamps();

            $table->index(['patient_id', 'doctor_id']);
            $table->index(['patient_id', 'external_doctor_id']);
        });

        // Table des messages
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->unsignedBigInteger('sender_id');
            $table->enum('sender_type', ['patient', 'doctor', 'external_doctor', 'system']);
            $table->text('content');
            $table->string('attachment_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        // Table des notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('external_doctor_id')->nullable()->constrained('external_doctors')->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'is_read']);
            $table->index(['doctor_id', 'is_read']);
            $table->index(['external_doctor_id', 'is_read']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('notifications');
    }
};