<?php


require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('super_admins')->get()->toArray();

if (empty($rows)) {
    echo "NO_SUPERADMINS\n";
    exit(0);
}

print_r($rows);
