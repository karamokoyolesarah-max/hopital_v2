<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ExternalDoctor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== TEST DE CONNEXION MÉDECIN EXTERNE ===\n\n";

// Vérifier s'il y a des médecins externes dans la base
$externalDoctors = ExternalDoctor::all();
$users = User::where('role', 'external_doctor')->get();

echo "Médecins externes dans external_doctors: " . $externalDoctors->count() . "\n";
echo "Utilisateurs avec rôle external_doctor: " . $users->count() . "\n\n";

if ($externalDoctors->isEmpty()) {
    echo "❌ Aucun médecin externe trouvé. Création d'un médecin de test...\n";

    // Créer un médecin externe de test
    $doctor = ExternalDoctor::create([
        'first_name' => 'Test',
        'last_name' => 'Doctor',
        'email' => 'test.doctor@example.com',
        'phone' => '0123456789',
        'password' => Hash::make('password'),
        'speciality' => 'Médecine générale',
        'license_number' => 'TEST123',
        'status' => 'approved',
        'is_active' => true,
    ]);

    // Créer le compte miroir dans users
    User::create([
        'name' => 'Dr. Test Doctor',
        'email' => 'test.doctor@example.com',
        'password' => $doctor->password,
        'role' => 'external_doctor',
        'phone' => '0123456789',
        'is_active' => true,
    ]);

    echo "✅ Médecin de test créé: test.doctor@example.com / password\n\n";
    $testDoctor = $doctor;
} else {
    $testDoctor = $externalDoctors->first();
    echo "Utilisation du médecin existant: {$testDoctor->first_name} {$testDoctor->last_name}\n";
    echo "Email: {$testDoctor->email}\n\n";
}

// Test de connexion
echo "🔐 Test de connexion...\n";

$credentials = [
    'email' => $testDoctor->email,
    'password' => 'password'
];

try {
    // Test avec le guard external_doctors
    if (Auth::guard('external_doctors')->attempt($credentials)) {
        echo "✅ Connexion réussie avec guard 'external_doctors'\n";
        $user = Auth::guard('external_doctors')->user();
        echo "   Utilisateur: {$user->first_name} {$user->last_name}\n";
        echo "   Statut: {$user->status}\n";
        echo "   Actif: " . ($user->is_active ? 'Oui' : 'Non') . "\n";

        // Test de redirection vers le dashboard
        if ($user->status === 'approved' && $user->is_active) {
            echo "✅ Redirection vers dashboard autorisée\n";
            echo "   Route cible: external.doctor.dashboard\n";
        } else {
            echo "⚠️ Redirection vers page d'attente\n";
        }

        Auth::guard('external_doctors')->logout();
    } else {
        echo "❌ Échec de connexion avec guard 'external_doctors'\n";
    }
} catch (\Exception $e) {
    echo "❌ Erreur lors de la connexion: " . $e->getMessage() . "\n";
}

// Test avec le guard par défaut (users)
echo "\n🔐 Test avec guard 'web' (users)...\n";

$userAccount = User::where('email', $testDoctor->email)->first();
if ($userAccount) {
    if (Auth::attempt(['email' => $testDoctor->email, 'password' => 'password'])) {
        echo "✅ Connexion réussie avec guard 'web'\n";
        $user = Auth::user();
        echo "   Utilisateur: {$user->name}\n";
        echo "   Rôle: {$user->role}\n";

        // Test de redirection selon le rôle
        $redirectRoute = match($user->role) {
            'external_doctor' => 'external.doctor.dashboard',
            default => 'dashboard'
        };
        echo "   Redirection vers: {$redirectRoute}\n";

        Auth::logout();
    } else {
        echo "❌ Échec de connexion avec guard 'web'\n";
    }
} else {
    echo "❌ Aucun compte utilisateur trouvé pour cet email\n";
}

echo "\n=== FIN DU TEST ===\n";
