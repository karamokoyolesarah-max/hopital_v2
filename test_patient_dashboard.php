<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

echo "=== TEST PATIENT DASHBOARD - INVOICES RELATIONSHIP ===\n\n";

// Vérifier s'il y a des patients dans la base
$patients = Patient::all();

echo "Nombre de patients dans la base: " . $patients->count() . "\n\n";

if ($patients->isEmpty()) {
    echo "❌ Aucun patient trouvé. Création d'un patient de test...\n";

    // Créer un patient de test
    $patient = Patient::create([
        'ipu' => Patient::generateIpu(),
        'name' => 'Test',
        'first_name' => 'Patient',
        'email' => 'test.patient@example.com',
        'phone' => '0123456789',
        'password' => bcrypt('password'),
        'is_active' => true,
    ]);

    echo "✅ Patient de test créé: {$patient->first_name} {$patient->name} (IPU: {$patient->ipu})\n\n";
    $testPatient = $patient;
} else {
    $testPatient = $patients->first();
    echo "Utilisation du patient existant: {$testPatient->first_name} {$testPatient->name}\n";
    echo "IPU: {$testPatient->ipu}\n\n";
}

// Test de la méthode invoices()
echo "🔍 Test de la méthode invoices()...\n";

try {
    // Test du count
    $invoiceCount = $testPatient->invoices()->count();
    echo "✅ invoices()->count() fonctionne: {$invoiceCount} factures\n";

    // Test du latest()->take(3)->get()
    $recentInvoices = $testPatient->invoices()->latest()->take(3)->get();
    echo "✅ invoices()->latest()->take(3)->get() fonctionne: {$recentInvoices->count()} factures récupérées\n";

    echo "🎉 Toutes les méthodes invoices() fonctionnent correctement!\n";

} catch (\Exception $e) {
    echo "❌ Erreur lors de l'appel à invoices(): " . $e->getMessage() . "\n";
    echo "   Type d'erreur: " . get_class($e) . "\n";
}

// Test de simulation du dashboard
echo "\n🏥 Simulation du dashboard patient...\n";

try {
    // Simuler les appels du contrôleur
    $totalAppointments = $testPatient->appointments()->count();
    $totalPrescriptions = $testPatient->prescriptions()->count();
    $totalDocuments = $testPatient->documents()->count();
    $totalInvoices = $testPatient->invoices()->count();

    echo "✅ Statistiques récupérées:\n";
    echo "   - Rendez-vous: {$totalAppointments}\n";
    echo "   - Prescriptions: {$totalPrescriptions}\n";
    echo "   - Documents: {$totalDocuments}\n";
    echo "   - Factures: {$totalInvoices}\n";

    $recentInvoices = $testPatient->invoices()->latest()->take(3)->get();
    echo "✅ Factures récentes récupérées: {$recentInvoices->count()}\n";

    echo "🎉 Le dashboard patient devrait fonctionner sans erreur BadMethodCallException!\n";

} catch (\Exception $e) {
    echo "❌ Erreur lors de la simulation du dashboard: " . $e->getMessage() . "\n";
    echo "   Type d'erreur: " . get_class($e) . "\n";
}

echo "\n=== FIN DU TEST ===\n";
