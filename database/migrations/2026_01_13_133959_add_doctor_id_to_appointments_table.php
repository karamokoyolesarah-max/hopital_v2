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
            // Vérifier si la colonne doctor_id n'existe pas déjà
            if (!Schema::hasColumn('appointments', 'doctor_id')) {
                // On ajoute la colonne doctor_id (clé étrangère)
                // 'constrained' assume que la table s'appelle 'doctors'
                // 'nullOnDelete' permet de garder le RDV même si le docteur est supprimé
                $table->foreignId('doctor_id')
                      ->nullable()
                      ->after('id') // Place la colonne après l'ID
                      ->constrained('doctors')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // On supprime d'abord la contrainte, puis la colonne
            $table->dropForeign(['doctor_id']);
            $table->dropColumn('doctor_id');
        });
    }
};