<?php
$host = 'aws-0-ap-southeast-1.pooler.supabase.com';
$user = 'postgres.uaoikjhmemvtxnnqdgfr';
$pass = 'Ari@19027';
$dbname = 'postgres';
$port = '6543';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
    echo "SUCCESS!\n";
} catch (PDOException $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
