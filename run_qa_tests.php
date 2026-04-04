<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

function logStatus($testName, $status, $desc = '') {
    $icon = $status ? "[PASS]" : "[FAIL]";
    echo "$icon $testName - $desc\n";
    if (!$status) exit(1);
}

echo "=== MEMULAI SYSTEM QA/QC GANDARIA CITY ===\n\n";

$spv = User::where('role', 'SPV')->first();
$cpm = User::where('role', 'CPM')->first();
$it = User::where('role', 'IT')->first();

if (!$spv || !$cpm || !$it) {
    echo "Gagal menemukan data user SPV / CPM / IT\n";
    exit(1);
}

// ---------------------------------------------------------
// TEST 1: Modul Asset Findings (Temuan)
// ---------------------------------------------------------

echo "1. Uji Modul Asset Findings Murni...\n";
auth()->login($spv);

// SPV Creates Finding
$dummyPhoto = UploadedFile::fake()->image('test_finding.jpg');
$request = \Illuminate\Http\Request::create('/findings', 'POST', [
    'location' => 'Lantai 1 - Gate A',
    'asset_type' => 'Infrastruktur',
    'description' => 'Kerusakan pintu kaca',
    'finding_date' => date('Y-m-d'),
], [], ['photo' => $dummyPhoto]);
$controller = new \App\Http\Controllers\AssetFindingController();
$response = $controller->store($request);

$finding = AssetFinding::latest()->first();
logStatus("SPV Buat Temuan", $finding && $finding->location == 'Lantai 1 - Gate A', "Temuan terbuat: " . ($finding->finding_code ?? 'None'));
logStatus("Status Default", $finding->status == 'Pending Approval', "Status saat ini: {$finding->status}");
logStatus("Dokumen Foto Terekam", !empty($finding->photo), "Path: {$finding->photo}");

// SPV Edits pending finding (Should allow)
auth()->login($spv);
$editRequest = \Illuminate\Http\Request::create('/findings/'.$finding->id, 'PUT', [
    'location' => 'Lantai 1 - Gate B',
    'status' => 'Pending Approval'
]);
$controller->update($editRequest, $finding);
$finding->refresh();
logStatus("SPV Edit Temuan Pending", $finding->location == 'Lantai 1 - Gate B', "Lokasi berubah");

// CPM Approves finding
auth()->login($cpm);
$controller->approve($finding);
$finding->refresh();
logStatus("CPM Approve Temuan", $finding->status == 'Open', "Status saat ini: {$finding->status}");

// SPV Tries to Edit approved finding (Should Block)
auth()->login($spv);
try {
    $controller->update($editRequest, $finding);
    logStatus("SPV Block Edit (Open)", false, "SPV harusnya diblokir");
} catch (\Exception $e) {
    logStatus("SPV Block Edit (Open)", $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException, "Akses ditolak dengan 403 (Benar)");
}

// ---------------------------------------------------------
// TEST 2: Modul Berita Acara (BA)
// ---------------------------------------------------------
echo "\n2. Uji Modul Berita Acara...\n";
auth()->login($spv);

// SPV Creates BA
$dummyDoc = UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf');
$baRequest = \Illuminate\Http\Request::create('/ba', 'POST', [
    'ba_type' => 'Insiden Keamanan',
    'incident_date' => date('Y-m-d'),
    'customer_name' => 'QA Tester',
    'chronology' => 'Test unggah dokumen PDF',
], [], ['attachment' => $dummyDoc]);
$baController = new \App\Http\Controllers\BeritaAcaraController();
$baController->store($baRequest);

$ba = BeritaAcara::latest()->first();
logStatus("SPV Buat BA", $ba && $ba->customer_name == 'QA Tester', "BA terbuat: " . ($ba->ba_number ?? 'None'));
logStatus("Status Default BA", $ba->status == 'Submitted', "Status BA: {$ba->status}");
logStatus("Dokumen Attach", !empty($ba->attachment), "Path: {$ba->attachment}");

// CPM Approves BA
auth()->login($cpm);
$baController->approve($ba);
$ba->refresh();
logStatus("CPM Approve BA", $ba->status == 'Processed', "Status BA saat ini: {$ba->status}");

// SPV Tries to edit Progress (Should Block)
auth()->login($spv);
$baEditRequest = \Illuminate\Http\Request::create('/ba/'.$ba->id, 'PUT', [
    'status' => 'Done'
]);
try {
    $baController->update($baEditRequest, $ba);
    logStatus("SPV Block Edit BA (Processed)", false, "SPV harusnya dilarang");
} catch (\Exception $e) {
    logStatus("SPV Block Edit BA (Processed)", $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException, "Sistem memblokir SPV dengan 403 (Benar)");
}

// CPM Cancel Approval
auth()->login($cpm);
$baController->cancelApprove($ba);
$ba->refresh();
logStatus("CPM Cancel Approve BA", $ba->status == 'Submitted', "Status BA kembali ke: {$ba->status}");

// ---------------------------------------------------------
// TEST 3: Dashboard Analytics
// ---------------------------------------------------------
echo "\n3. Uji Konsistensi Dashboard...\n";
// Create explicitly a Done BA and Done finding
BeritaAcara::create(['ba_number' => 'TEST/BA/01', 'ba_type' => 'Lainnya', 'incident_date' => date('Y-m-d'), 'customer_name' => 'xxx', 'chronology' => 'xxx', 'status' => 'Done', 'pic_id' => $spv->id]);
AssetFinding::create(['finding_code' => 'T-TEST-01', 'location' => 'A', 'asset_type' => 'A', 'description' => 'A', 'finding_date' => date('Y-m-d'), 'reporter' => 'A', 'status' => 'Pending Approval', 'pic_id' => $spv->id]);

$dashController = new \App\Http\Controllers\DashboardController();
$view = $dashController->index();
$stats = $view->getData()['stats'];

logStatus("Stats: Open Findings terhitung", isset($stats['findings_open']), "Value: ".$stats['findings_open']);
logStatus("Stats: Pending Approval terbaca", current(array_filter([$stats['findings_pending']] ?? [])) >= 1 || $stats['findings_pending'] == AssetFinding::where('status', 'Pending Approval')->count(), "Value BA Pending: ".$stats['findings_pending']);

echo "\n== SEMUA TEST SELESAI DAN VALID ==\n";
