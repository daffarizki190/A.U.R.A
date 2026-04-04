<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "=== LOGIN TEST SCRIPT ===\n\n";

$credentials_to_test = [
    ['email' => 'spv1@gandariacity.com', 'password' => 'password123'],
    ['email' => 'cpm@gandariacity.com', 'password' => 'password123'],
];

foreach ($credentials_to_test as $cred) {
    echo "Testing login for: " . $cred['email'] . "... ";
    
    // Auth::attempt will hit Supabase since that's what's in .env
    if (Auth::attempt($cred)) {
        echo "[SUCCESS] Authenticated!\n";
    } else {
        echo "[FAILURE] Invalid credentials.\n";
        
        $user = User::where('email', $cred['email'])->first();
        if ($user) {
            echo "   Debug: User found. Hash in DB: " . substr($user->password, 0, 20) . "...\n";
        } else {
            echo "   Debug: User not found in DB.\n";
        }
    }
}

echo "\nTest Completed.\n";
