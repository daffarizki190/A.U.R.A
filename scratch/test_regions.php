<?php
$regions = [
    'aws-0-ap-southeast-3.pooler.supabase.com',
    'aws-0-ap-southeast-1.pooler.supabase.com',
    'aws-0-ap-southeast-2.pooler.supabase.com',
    'aws-0-ap-northeast-1.pooler.supabase.com',
    'aws-0-ap-northeast-2.pooler.supabase.com',
    'aws-0-ap-south-1.pooler.supabase.com',
    'aws-0-us-east-1.pooler.supabase.com',
    'aws-0-us-east-2.pooler.supabase.com',
    'aws-0-us-west-1.pooler.supabase.com',
    'aws-0-us-west-2.pooler.supabase.com',
    'aws-0-eu-central-1.pooler.supabase.com',
    'aws-0-eu-west-1.pooler.supabase.com',
    'aws-0-eu-west-2.pooler.supabase.com',
    'aws-0-eu-west-3.pooler.supabase.com',
    'aws-0-eu-north-1.pooler.supabase.com',
    'aws-0-sa-east-1.pooler.supabase.com',
    'aws-0-ca-central-1.pooler.supabase.com',
    'aws-0-me-central-1.pooler.supabase.com',
    'aws-0-af-south-1.pooler.supabase.com',
];

$user = 'postgres.uaoikjhmemvtxnnqdgfr';
$pass = 'Jakarta@19027';
$dbname = 'postgres';
$port = '6543';

foreach ($regions as $host) {
    echo "Testing $host... ";
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    try {
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_TIMEOUT => 5]);
        echo "SUCCESS!\n";
        exit(0);
    } catch (PDOException $e) {
        echo "FAILED: " . $e->getMessage() . "\n";
    }
}
