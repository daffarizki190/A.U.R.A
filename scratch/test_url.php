<?php
$url = 'postgresql://postgres.uaoikjhmemvtxnnqdgfr:Jakarta%4019027@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres?sslmode=require';

try {
    $pdo = new PDO($url);
    echo "SUCCESS!\n";
} catch (PDOException $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}
