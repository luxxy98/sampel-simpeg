<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Absensi\Absensi;
use App\Services\Absensi\AbsensiService;

echo "=== TEST UPDATE ABSENSI ===\n\n";

// Get first absensi
$absensi = Absensi::first();
if (!$absensi) {
    echo "No absensi found\n";
    exit;
}

echo "Found absensi ID: " . $absensi->id_absensi . "\n";
echo "Current total_lembur: " . ($absensi->total_lembur ?? 'null') . "\n\n";

// Test direct update
try {
    echo "Testing direct Eloquent update...\n";
    $absensi->total_lembur = 1.5;
    $absensi->save();
    echo "✅ Direct update SUCCESS!\n\n";
} catch (\Exception $e) {
    echo "❌ Direct update failed: " . $e->getMessage() . "\n\n";
}

// Test service update
try {
    echo "Testing AbsensiService update...\n";
    $service = app(AbsensiService::class);
    
    $data = [
        'tanggal' => $absensi->tanggal,
        'id_jadwal_karyawan' => $absensi->id_jadwal_karyawan,
        'id_sdm' => $absensi->id_sdm,
        'total_jam_kerja' => $absensi->total_jam_kerja,
        'total_terlambat' => $absensi->total_terlambat,
        'total_pulang_awal' => $absensi->total_pulang_awal,
        'total_lembur' => 2.0,
    ];
    
    $service->update($absensi, $data, []);
    echo "✅ Service update SUCCESS!\n";
} catch (\Exception $e) {
    echo "❌ Service update failed:\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
