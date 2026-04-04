<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    DB::statement("ALTER TABLE berita_acaras DROP CONSTRAINT IF EXISTS berita_acaras_status_check;");
    DB::statement("ALTER TABLE berita_acaras ALTER COLUMN status TYPE VARCHAR(255);");
    echo "SUCCESS\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
