<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Users in database:\n";
$users = User::all();
foreach($users as $user) {
    echo "ID: {$user->id} | Email: {$user->email} | Name: {$user->name} | Role: {$user->role}\n";
    echo "  Password Hash: {$user->password}\n";
    $isValid = Hash::check('password123', $user->password) ? 'YES' : 'NO';
    echo "  Password matches 'password123'? $isValid\n\n";
}
