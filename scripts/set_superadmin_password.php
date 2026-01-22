<?php

if ($argc < 3) {
    echo "Usage: php set_superadmin_password.php email new_password\n";
    exit(1);
}

$email = $argv[1];
$new = $argv[2];

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$updated = DB::table('super_admins')->where('email', $email)->update(['password' => Hash::make($new)]);

if ($updated) {
    echo "PASSWORD_UPDATED\n";
} else {
    echo "NO_SUPERADMIN_FOUND\n";
}
