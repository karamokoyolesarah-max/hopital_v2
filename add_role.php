<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

DB::statement("ALTER TABLE external_doctors ADD COLUMN role VARCHAR(255) DEFAULT 'médecin externe'");

echo "Column added successfully";
