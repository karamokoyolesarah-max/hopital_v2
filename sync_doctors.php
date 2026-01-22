<?php
// sync_doctors.php

use App\Models\ExternalDoctor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Chargement de l'environnement Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- DEBUT DE LA SYNCHRONISATION ---\n";

// Récupérer tous les médecins déjà approuvés
$approvedDoctors = ExternalDoctor::where('status', 'approved')->get();

foreach ($approvedDoctors as $doctor) {
    // On cherche si l'utilisateur existe déjà
    $user = User::where('email', $doctor->email)->first();

    if (!$user) {
        // Si l'utilisateur n'existe pas, on le crée
        User::create([
            'name'     => 'Dr. ' . $doctor->first_name . ' ' . $doctor->last_name,
            'email'    => $doctor->email,
            'password' => $doctor->password, // On récupère le hash existant
            'role'     => 'external_doctor',
            'phone'    => $doctor->phone,
            'is_active' => true,
        ]);
        echo "✅ Compte User créé pour : {$doctor->email}\n";
    } else {
        // Si l'utilisateur existe, on s'assure juste que son rôle est correct
        $user->update([
            'role' => 'external_doctor',
            'is_active' => true
        ]);
        echo "ℹ️ Compte existant mis à jour pour : {$doctor->email}\n";
    }
}

echo "--- TERMINE ! ---\n";