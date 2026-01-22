<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MedecinExterne;

echo "=== MÉDECINS EXTERNES DANS LA BASE DE DONNÉES ===\n\n";

$doctors = MedecinExterne::all();

if ($doctors->isEmpty()) {
    echo "❌ Aucun médecin externe trouvé dans la base de données.\n";
} else {
    echo "✅ " . $doctors->count() . " médecin(s) externe(s) trouvé(s) :\n\n";

    foreach ($doctors as $doctor) {
        echo "👨‍⚕️ Dr. {$doctor->prenom} {$doctor->nom}\n";
        echo "   📧 Email: {$doctor->email}\n";
        echo "   📞 Téléphone: {$doctor->telephone}\n";
        echo "   🏥 Spécialité: {$doctor->specialite}\n";
        echo "   🔢 Numéro d'ordre: {$doctor->numero_ordre}\n";
        echo "   📊 Statut: {$doctor->statut}\n";
        echo "   📅 Créé le: {$doctor->created_at}\n";
        echo "   ──────────────────────────────────\n";
    }
}

echo "\n=== TEST D'AUTHENTIFICATION ===\n\n";

// Test de connexion pour un médecin du nouveau seeder
$testDoctor = $doctors->where('email', 'dr.kouassi@hospitsis.com')->first();

if ($testDoctor) {
    echo "🔐 Test de connexion pour {$testDoctor->prenom} {$testDoctor->nom}...\n";

    $credentials = [
        'email' => $testDoctor->email,
        'password' => 'password' // Mot de passe du seeder
    ];

    if (Auth::guard('medecin_externe')->attempt($credentials)) {
        echo "✅ Connexion réussie !\n";
        $user = Auth::guard('medecin_externe')->user();
        echo "   Utilisateur connecté: {$user->prenom} {$user->nom}\n";
    } else {
        echo "❌ Échec de la connexion\n";
    }
} else {
    echo "❌ Médecin de test non trouvé\n";
}

echo "\n=== FIN DU TEST ===\n";
