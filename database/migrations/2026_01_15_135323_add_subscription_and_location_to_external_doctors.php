<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('external_doctors', function (Blueprint $table) {
            // Géolocalisation
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('location_updated_at')->nullable();

            // Statut en ligne
            $table->boolean('is_online')->default(false);

            // Abonnement (pour recevoir des rendez-vous)
            $table->boolean('has_active_subscription')->default(false);
            $table->date('subscription_start_date')->nullable();
            $table->date('subscription_end_date')->nullable();
            $table->enum('subscription_type', ['daily', 'weekly', 'monthly'])->nullable();
        });
    }

    public function down()
    {
        Schema::table('external_doctors', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'location_updated_at', 'is_online',
                'has_active_subscription', 'subscription_start_date', 
                'subscription_end_date', 'subscription_type'
            ]);
        });
    }
};