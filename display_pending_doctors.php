<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalDoctor;

echo "=== MÉDECINS EXTERNES EN ATTENTE D'APPROBATION ===\n\n";

$pendingDoctors = ExternalDoctor::where('status', 'pending')->get();

if ($pendingDoctors->isEmpty()) {
    echo "❌ Aucun médecin externe en attente d'approbation.\n";
} else {
    echo "✅ " . $pendingDoctors->count() . " médecin(s) externe(s) en attente d'approbation :\n\n";

    foreach ($pendingDoctors as $doctor) {
        echo "👨‍⚕️ Dr. {$doctor->first_name} {$doctor->last_name}\n";
        echo "   📧 Email: {$doctor->email}\n";
        echo "   📞 Téléphone: {$doctor->phone}\n";
        echo "   🏥 Spécialité: {$doctor->speciality}\n";
        echo "   🔢 Numéro de licence: {$doctor->license_number}\n";
        echo "   📊 Statut: {$doctor->status}\n";
        echo "   📅 Créé le: {$doctor->created_at}\n";
        echo "   ──────────────────────────────────\n";
    }
}

echo "\n=== FIN DE LA LISTE ===\n";
