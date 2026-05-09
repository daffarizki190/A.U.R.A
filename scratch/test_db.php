<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Connection successful!\n";
    $users = Illuminate\Support\Facades\DB::table('users')->get();
    echo "Users count: " . $users->count() . "\n";
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
