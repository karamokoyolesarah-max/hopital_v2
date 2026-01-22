<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing the fixed query...\n";

$doctor = App\Models\ExternalDoctor::first();
if ($doctor) {
    echo "Doctor found: " . $doctor->email . "\n";

    try {
        $count = $doctor->appointments()->join('patients', 'appointments.patient_id', '=', 'patients.id')->whereNull('patients.deleted_at')->distinct('appointments.patient_id')->count();
        echo "Patient count: " . $count . "\n";
        echo "Query executed successfully!\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "No external doctors found\n";
}
