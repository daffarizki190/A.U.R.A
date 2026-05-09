<?php
$host = '3.109.171.244';
$port = '6543';
$dbname = 'postgres';
$user = 'postgres.wzamfrmfowimudzeqerz';
$pass = 'GandariaOutstanding_2026!';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass);
    echo "Connection successful!\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
