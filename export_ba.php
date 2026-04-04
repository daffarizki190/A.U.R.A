<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$bas = DB::table('berita_acaras')->get();
file_put_contents('ba_data.json', $bas->toJson());
