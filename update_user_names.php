<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

try {
    User::where('email', 'spv1@gandariacity.com')->update(['name' => 'Yamin']);
    User::where('email', 'spv2@gandariacity.com')->update(['name' => 'Akmal']);
    User::where('email', 'cpm@gandariacity.com')->update(['name' => 'Rizal']);
    User::where('email', 'it@gandariacity.com')->update(['name' => 'Irvandi']);

    echo "User names updated successfully!\n";
} catch (\Exception $e) {
    echo "Error updating names: " . $e->getMessage() . "\n";
}
