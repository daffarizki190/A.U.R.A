<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "--- FINAL AUTH VERIFICATION ---\n";

$email = 'spv1@gandariacity.com';
$password = 'password123';

try {
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "Updating password for $email...\n";
        $user->password = Hash::make($password);
        $user->save();
        
        $freshUser = User::where('email', $email)->first();
        echo "Hash saved: " . $freshUser->password . "\n";
        
        echo "Testing Auth::attempt()... ";
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            echo "[SUCCESS] Authenticated locally!\n";
        } else {
            echo "[FAILED] Could not authenticate even after update.\n";
        }
    } else {
        echo "User not found.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
