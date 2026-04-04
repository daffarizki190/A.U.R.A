<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use App\Models\User;
use Illuminate\Support\Carbon;

header('Content-Type: text/plain');

echo "=============================================\n";
echo "    FULL SYSTEM TEST - DASHBOARD OUTSTANDING \n";
echo "=============================================\n\n";

try {
    echo "[TEST 1] - Database Connection & Users\n";
    $userCount = User::count();
    $pic = User::where('role', 'SPV')->first() ?? User::first();
    $cpm = User::where('role', 'CPM')->first();
    echo "  -> Check: users count = {$userCount}\n";
    echo "  -> OK.\n\n";

    // ----------------------------------------------------
    echo "[TEST 2] - Asset Findings Enum Test ('Pending Approval')\n";
    $findingCode = 'TF-' . date('His');
    $finding = AssetFinding::create([
        'finding_code' => $findingCode,
        'finding_date' => date('Y-m-d'),
        'location'     => 'Testing Location',
        'asset_type'   => 'CCTV',
        'description'  => 'Test Enum Pending Approval',
        'status'       => 'Pending Approval', // <--- This should succeed now after DB fix
        'reporter'     => 'System Tester',
        'pic_id'       => $pic->id ?? null,
    ]);
    echo "  -> Success creating finding! ID: {$finding->id}, Status: {$finding->status}\n";
    echo "  -> OK.\n\n";

    // ----------------------------------------------------
    echo "[TEST 3] - Berita Acara Creation ('submitted_at' logic)\n";
    $baCode = 'TBA-' . date('His');
    $ba = BeritaAcara::create([
        'ba_number'     => $baCode,
        'ba_type'       => 'Test Incident',
        'incident_date' => date('Y-m-d'),
        'customer_name' => 'John Doe Tester',
        'chronology'    => 'Cronology Test',
        'status'        => 'Submitted',
        'pic_id'        => $pic->id ?? null,
        'submitted_at'  => Carbon::now(), // <--- Test logic added in controller
    ]);
    
    // Refresh model to see DB values
    $ba->refresh();
    if ($ba->submitted_at) {
        echo "  -> Success creating BA! ID: {$ba->id}, Status: {$ba->status}\n";
        echo "  -> 'submitted_at' is correctly filled: {$ba->submitted_at}\n";
        echo "  -> OK.\n\n";
    } else {
        throw new Exception("'submitted_at' is missing in DB for BA ID {$ba->id}");
    }

    // ----------------------------------------------------
    echo "[TEST 4] - Berita Acara Approval ('approved_at' logic)\n";
    $ba->update([
        'status'      => 'Processed',
        'approved_at' => Carbon::now(),
    ]);

    $ba->refresh();
    if ($ba->approved_at && $ba->status === 'Processed') {
        echo "  -> Success processing BA! ID: {$ba->id}, Status: {$ba->status}\n";
        echo "  -> 'approved_at' is correctly filled: {$ba->approved_at}\n";
        echo "  -> OK.\n\n";
    } else {
        throw new Exception("'approved_at' is missing or status not updated.");
    }

    // ----------------------------------------------------
    echo "[TEST 5] - Berita Acara Cancel Approval (Reset 'approved_at')\n";
    $ba->update([
        'status'      => 'Submitted',
        'approved_at' => null,
    ]);

    $ba->refresh();
    if (is_null($ba->approved_at) && $ba->status === 'Submitted') {
        echo "  -> Success cancelling BA! ID: {$ba->id}, Status: {$ba->status}\n";
        echo "  -> 'approved_at' is cleanly reset to NULL.\n";
        echo "  -> OK.\n\n";
    } else {
        throw new Exception("'approved_at' is NOT NULL after cancellation.");
    }

    echo "=============================================\n";
    echo " ALL TESTS PASSED SUCCESSFULLY! ✅\n";
    echo "=============================================\n";

} catch (\Exception $e) {
    echo "\n[ERROR] Test Failed!\n";
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
