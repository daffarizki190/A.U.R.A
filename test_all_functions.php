<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use App\Models\User;

header('Content-Type: text/plain');

try {
    echo "=== SIAB TESTING SCRIPT ===\n\n";

    echo "1. Testing Database Connection...\n";
    $userCount = User::count();
    echo "   [OK] Users in database: $userCount\n";

    echo "\n2. Testing Create Asset Finding...\n";
    $findingCode = 'TEST-' . date('His');
    $finding = AssetFinding::create([
        'finding_code' => $findingCode,
        'finding_date' => date('Y-m-d'),
        'location' => 'Gate Utama',
        'asset_type' => 'Barrier Gate',
        'description' => 'Testing function from script.',
        'status' => 'Open',
        'reporter' => 'System Tester'
    ]);
    echo "   [OK] Created Finding with Code: " . $finding->finding_code . "\n";

    echo "\n3. Testing Create Berita Acara...\n";
    $baNumber = 'BA-TEST-' . date('His');
    $ba = BeritaAcara::create([
        'ba_number' => $baNumber,
        'ba_type' => 'Kehilangan',
        'incident_date' => date('Y-m-d'),
        'customer_name' => 'Test Customer',
        'license_plate' => 'B 1234 TEST',
        'chronology' => 'Testing BA function.',
        'status' => 'Draft'
    ]);
    echo "   [OK] Created BA with Number: " . $ba->ba_number . "\n";

    echo "\n4. Verifying Data Persistence...\n";
    $checkFinding = AssetFinding::where('finding_code', $findingCode)->first();
    $checkBA = BeritaAcara::where('ba_number', $baNumber)->first();

    if ($checkFinding && $checkBA) {
        echo "   [SUCCESS] All functions verified. Data persistent in Supabase.\n";
    } else {
        echo "   [FAILURE] Data not found after creation.\n";
    }

} catch (\Exception $e) {
    echo "   [ERROR] " . $e->getMessage() . "\n";
}
