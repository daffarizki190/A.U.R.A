<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    $newHash = Hash::make('password123');
    User::query()->update(['password' => $newHash]);
    echo "All passwords updated to 'password123' successfully!\n";
} catch (\Exception $e) {
    echo "Error updating passwords: " . $e->getMessage() . "\n";
}
