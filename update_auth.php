<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== USER AUTH UPDATE ===\n\n";

$emails = [
    'spv1@gandariacity.com',
    'spv2@gandariacity.com',
    'cpm@gandariacity.com',
    'it@gandariacity.com'
];

$password = 'password123';

try {
    foreach ($emails as $email) {
        echo "Updating $email... ";
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($password);
            $user->save();
            echo "[OK] New hash starts with: " . substr($user->password, 0, 10) . "\n";
        } else {
            echo "[NOT FOUND]\n";
        }
    }
} catch (\Exception $e) {
    echo "\n[ERROR] " . $e->getMessage() . "\n";
}
