<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Auth;

echo "--- VERIFIKASI ALUR APPROVAL --- \n\n";

try {
    // 1. Simulasikan login sebagai SPV
    $spv = User::where('role', 'SPV')->first();
    if (!$spv) throw new Exception("User SPV tidak ditemukan");
    
    Auth::login($spv);
    echo "1. Login sebagai SPV: " . $spv->name . " (ID: " . $spv->id . ")\n";

    // 2. Buat Temuan Asset (Logika seperti di controller store)
    $finding = AssetFinding::create([
        'finding_code' => 'TEST-999',
        'location' => 'Gate Verifikasi',
        'asset_type' => 'CCTV',
        'description' => 'Test Verifikasi',
        'finding_date' => now(),
        'reporter' => $spv->name,
        'pic_id' => auth()->id(),
        'status' => 'Pending Approval'
    ]);
    echo "2. Temuan dibuat. PIC: " . $finding->pic->name . ", Status: " . $finding->status . "\n";

    // 3. Simulasikan login sebagai CPM
    $cpm = User::where('role', 'CPM')->first();
    if (!$cpm) throw new Exception("User CPM tidak ditemukan");
    
    Auth::login($cpm);
    echo "3. Login sebagai CPM: " . $cpm->name . "\n";

    // 4. Lakukan Approval via Controller
    echo "4. Menjalankan fungsi approve()...\n";
    $controller = new App\Http\Controllers\AssetFindingController();
    $controller->approve($finding);
    
    $finding->refresh();
    echo "   Status Akhir Temuan: " . $finding->status . " (Ekspektasi: Open)\n";

    if ($finding->status === 'Open') {
        echo "\n[SUCCESS] Verifikasi Asset Finding Berhasil!\n";
    }

    // 5. Ulangi untuk Berita Acara
    Auth::login($spv);
    $ba = BeritaAcara::create([
        'ba_number' => 'BA/TEST/999',
        'ba_type' => 'Kehilangan',
        'incident_date' => now(),
        'customer_name' => 'Budi Test',
        'chronology' => 'Testing chronology',
        'pic_id' => auth()->id(),
        'status' => 'Pending Approval'
    ]);
    echo "\n5. BA dibuat. PIC: " . $ba->pic->name . ", Status: " . $ba->status . "\n";

    Auth::login($cpm);
    echo "6. Login CPM & Berikan Approval ke BA...\n";
    $baController = new App\Http\Controllers\BeritaAcaraController();
    $baController->approve($ba);

    $ba->refresh();
    echo "   Status Akhir BA: " . $ba->status . " (Ekspektasi: Approved)\n";

    if ($ba->status === 'Approved') {
        echo "\n[SUCCESS] Verifikasi Berita Acara Berhasil!\n";
    }

    // Cleanup
    $finding->delete();
    $ba->delete();

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
