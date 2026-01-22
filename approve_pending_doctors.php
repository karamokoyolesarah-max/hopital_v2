<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalDoctor;

echo "=== APPROBATION DES MÉDECINS EXTERNES EN ATTENTE ===\n\n";

$pendingDoctors = ExternalDoctor::where('status', 'pending')->get();

if ($pendingDoctors->isEmpty()) {
    echo "❌ Aucun médecin externe en attente d'approbation.\n";
} else 
    echo "✅ " . $pendingDoctors->count() . " médecin(s) externe(s) en attente d'approbation trouvé(s).\n\n";
// approve_pending_doctors.php

// ... (garder le début du fichier identique) ...

foreach ($pendingDoctors as $doctor) {
    echo "🔄 Approbation de Dr. {$doctor->first_name} {$doctor->last_name}...\n";

    // 1. Activer le profil médecin externe
    $doctor->update(['status' => 'approved', 'is_active' => true]);

    // 2. Activer le compte utilisateur correspondant
    $user = \App\Models\User::where('email', $doctor->email)->first();
    if ($user) {
        $user->update(['is_active' => true]);
        echo "✅ Compte utilisateur activé.\n";
    }

    echo "✅ Dr. {$doctor->first_name} {$doctor->last_name} est maintenant opérationnel !\n";
    echo "   ──────────────────────────────────\n";
}