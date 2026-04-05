<?php
// fix_users.php - Jalankan dengan: php artisan tinker --execute="require 'fix_users.php';"

use Illuminate\Support\Facades\DB;

// Update nama-nama yang masih pendek / belum lengkap
$updates = [
    ['old' => 'Rizal',    'new' => 'Rizal Maulana'],
    ['old' => 'Irvandi',  'new' => 'Irvandi Maulana'],
    ['old' => 'Yamin',    'new' => 'Akhmad Nuryamin'],
    ['old' => 'Akmal',    'new' => 'Muhammad Akmal Feruzi'],
    ['old' => 'System Tester', 'new' => 'System Tester'], // Biarkan jika sudah benar
];

echo "=== CEK DATA USER SAAT INI ===\n";
$users = DB::table('users')->get(['id', 'name', 'email', 'role']);
foreach ($users as $u) {
    echo "ID:{$u->id} | {$u->name} | {$u->email} | {$u->role}\n";
}

echo "\n=== UPDATE NAMA ===\n";
foreach ($updates as $item) {
    $count = DB::table('users')->where('name', $item['old'])->count();
    if ($count > 0) {
        DB::table('users')->where('name', $item['old'])->update(['name' => $item['new']]);
        echo "✓ Updated: '{$item['old']}' → '{$item['new']}'\n";
    }
}

echo "\n=== DATA USER SETELAH UPDATE ===\n";
$users = DB::table('users')->get(['id', 'name', 'email', 'role']);
foreach ($users as $u) {
    echo "ID:{$u->id} | {$u->name} | {$u->email} | {$u->role}\n";
}
echo "\nDone!\n";
