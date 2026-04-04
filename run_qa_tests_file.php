<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

$logFile = __DIR__ . '/qa_execution.log';
file_put_contents($logFile, "=== MEMULAI SYSTEM QA/QC GANDARIA CITY ===\n\n");

function logStatus($testName, $status, $desc = '') {
    global $logFile;
    $icon = $status ? "[PASS]" : "[FAIL]";
    $msg = "$icon $testName - $desc\n";
    file_put_contents($logFile, $msg, FILE_APPEND);
    if (!$status) exit(1);
}

$spv = User::where('role', 'SPV')->first();
$cpm = User::where('role', 'CPM')->first();
$it = User::where('role', 'IT')->first();

if (!$spv || !$cpm || !$it) {
    file_put_contents($logFile, "Gagal menemukan data user SPV / CPM / IT\n", FILE_APPEND);
    exit(1);
}

// ---------------------------------------------------------
// TEST 1: Modul Asset Findings (Temuan)
// ---------------------------------------------------------
file_put_contents($logFile, "1. Uji Modul Asset Findings Murni...\n", FILE_APPEND);
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
$controller->store($request);

$finding = AssetFinding::latest()->first();
logStatus("SPV Buat Temuan", $finding && $finding->location == 'Lantai 1 - Gate A', "Temuan terbuat: " . ($finding->finding_code ?? 'None'));
logStatus("Status Default", $finding->status == 'Pending Approval', "Status saat ini: {$finding->status}");
logStatus("Dokumen Foto Terekam", !empty($finding->photo), "Path: {$finding->photo}");

// SPV Edits pending finding
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

// SPV Tries to Edit approved finding
auth()->login($spv);
$blocked = false;
try {
    $controller->update($editRequest, $finding);
} catch (\Exception $e) {
    $blocked = true;
}
logStatus("SPV Block Edit (Open)", $blocked, "Akses ditolak (Benar)");

// ---------------------------------------------------------
// TEST 2: Modul Berita Acara (BA)
// ---------------------------------------------------------
file_put_contents($logFile, "\n2. Uji Modul Berita Acara...\n", FILE_APPEND);
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
logStatus("CPM Approve BA", $ba->status == 'Processed', "Status BA: {$ba->status}");

// SPV Tries to edit Progress
auth()->login($spv);
$baEditRequest = \Illuminate\Http\Request::create('/ba/'.$ba->id, 'PUT', [
    'status' => 'Done'
]);
$blockedBa = false;
try {
    $baController->update($baEditRequest, $ba);
} catch (\Exception $e) {
    $blockedBa = true;
}
logStatus("SPV Block Edit BA (Processed)", $blockedBa, "Sistem memblokir (Benar)");

// CPM Cancel Approval
auth()->login($cpm);
$baController->cancelApprove($ba);
$ba->refresh();
logStatus("CPM Cancel Approve BA", $ba->status == 'Submitted', "Kembali ke: {$ba->status}");

// ---------------------------------------------------------
// TEST 3: Dashboard Analytics
// ---------------------------------------------------------
file_put_contents($logFile, "\n3. Uji Konsistensi Dashboard...\n", FILE_APPEND);
$dashController = new \App\Http\Controllers\DashboardController();
$view = $dashController->index();
$stats = $view->getData()['stats'];

logStatus("Stats: Open Findings terhitung", isset($stats['findings_open']), "Value: ".$stats['findings_open']);
logStatus("Stats: Pending Approval terhitung", isset($stats['findings_pending']), "Value BA Pending: ".$stats['findings_pending']);

file_put_contents($logFile, "\n== SEMUA TEST SELESAI DAN VALID ==\n", FILE_APPEND);
echo "DONE";
