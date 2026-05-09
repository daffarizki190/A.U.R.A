<?php
$host = 'db.uaoikjhmemvtxnnqdgfr.supabase.co';
$user = 'postgres';
$pass = 'Ari@19027';
$dbname = 'postgres';
$port = '5432';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
    echo "SUCCESS!\n";
} catch (PDOException $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
