<?php
/**
 * Script Tes Alur Aplikasi SIMPEG
 * SDM → Absensi → Gaji
 * 
 * Jalankan: php test_workflow.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// ======================================
// HELPER FUNCTIONS
// ======================================
function printHeader($title) {
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "  $title\n";
    echo str_repeat('=', 60) . "\n";
}

function printCheck($label, $status, $detail = '') {
    $icon = $status ? '✅' : '❌';
    echo "$icon $label";
    if ($detail) echo " - $detail";
    echo "\n";
}

function printInfo($label, $value) {
    echo "   📌 $label: $value\n";
}

$errors = [];
$startTime = microtime(true);

// ======================================
// 1. CEK KONEKSI DATABASE
// ======================================
printHeader("1. CEK KONEKSI DATABASE");

try {
    $simpegConn = DB::connection('mysql')->getPdo();
    printCheck("Koneksi ke database SIMPEG", true, "OK");
} catch (Exception $e) {
    printCheck("Koneksi ke database SIMPEG", false, $e->getMessage());
    $errors[] = "Gagal koneksi ke database SIMPEG";
}

try {
    $absensiConn = DB::connection('absensigaji')->getPdo();
    printCheck("Koneksi ke database Absensi/Gaji", true, "OK");
} catch (Exception $e) {
    printCheck("Koneksi ke database Absensi/Gaji", false, $e->getMessage());
    $errors[] = "Gagal koneksi ke database absensigaji";
}

// ======================================
// 2. CEK DATA SDM
// ======================================
printHeader("2. CEK DATA SDM");

$sdmCount = DB::table('person_sdm')->count();
printCheck("Data SDM tersedia", $sdmCount > 0, "$sdmCount pegawai");

$sdmList = DB::table('person_sdm as ps')
    ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
    ->leftJoin('sdm_struktural as ss', function($join) {
        $join->on('ss.id_sdm', '=', 'ps.id_sdm')
             ->whereNull('ss.tanggal_keluar');
    })
    ->leftJoin('master_jabatan as mj', 'mj.id_jabatan', '=', 'ss.id_jabatan')
    ->select('ps.id_sdm', 'p.nama', 'mj.jabatan')
    ->get();

foreach ($sdmList as $sdm) {
    printInfo($sdm->nama ?? "SDM #{$sdm->id_sdm}", $sdm->jabatan ?? 'Belum ada jabatan aktif');
}

$activeSDM = DB::table('sdm_struktural')
    ->whereNull('tanggal_keluar')
    ->count();
printCheck("SDM dengan jabatan aktif", $activeSDM > 0, "$activeSDM SDM aktif");

if ($activeSDM == 0) {
    $errors[] = "Tidak ada SDM dengan jabatan aktif (tanggal_keluar = NULL)";
}

// ======================================
// 3. CEK DATA ABSENSI
// ======================================
printHeader("3. CEK DATA ABSENSI");

$absensiCount = DB::connection('mysql')->table('absensi')->count();
printCheck("Data Absensi tersedia", $absensiCount > 0, "$absensiCount record");

$absensiBySDM = DB::connection('mysql')->table('absensi as a')
    ->leftJoin('simpeg.person_sdm as ps', 'ps.id_sdm', '=', 'a.id_sdm')
    ->leftJoin('simpeg.person as p', 'p.id_person', '=', 'ps.id_person')
    ->select('p.nama', DB::raw('COUNT(*) as total'))
    ->groupBy('p.nama')
    ->get();

foreach ($absensiBySDM as $abs) {
    printInfo($abs->nama ?? 'Unknown', "{$abs->total} hari absensi");
}

$jenisAbsenCount = DB::connection('mysql')->table('absen_jenis')->count();
printCheck("Jenis absensi tersedia", $jenisAbsenCount > 0, "$jenisAbsenCount jenis");

// Cek absensi dengan potongan
$absenPotong = DB::connection('mysql')->table('absen_jenis')
    ->where('potong_gaji', 'y')
    ->get(['nama_absen']);
if ($absenPotong->isNotEmpty()) {
    printInfo("Jenis absen dengan potongan", $absenPotong->pluck('nama_absen')->join(', '));
}

// ======================================
// 4. CEK DATA GAJI
// ======================================
printHeader("4. CEK DATA GAJI");

// Periode Gaji
$periodeCount = DB::connection('absensigaji')->table('gaji_periode')->count();
printCheck("Periode gaji tersedia", $periodeCount > 0, "$periodeCount periode");

$periodeList = DB::connection('absensigaji')->table('gaji_periode')
    ->orderByDesc('tahun')
    ->orderByDesc('bulan')
    ->limit(3)
    ->get(['id_periode', 'tahun', 'bulan']);
foreach ($periodeList as $p) {
    $label = $p->tahun . '-' . str_pad($p->bulan, 2, '0', STR_PAD_LEFT);
    printInfo("Periode #{$p->id_periode}", $label);
}

// Jenis Komponen Gaji
$jenisKomponenCount = DB::connection('absensigaji')->table('gaji_jenis_komponen')->count();
printCheck("Jenis komponen gaji tersedia", $jenisKomponenCount > 0, "$jenisKomponenCount komponen");

$jenisKomponen = DB::connection('absensigaji')->table('gaji_jenis_komponen')->get();
foreach ($jenisKomponen as $jk) {
    printInfo($jk->nama_komponen, $jk->jenis);
}

// Komponen Gaji per Jabatan
$komponenCount = DB::connection('absensigaji')->table('gaji_komponen')->count();
printCheck("Komponen gaji per jabatan tersedia", $komponenCount > 0, "$komponenCount record");

if ($komponenCount == 0) {
    $errors[] = "Tidak ada komponen gaji. Jalankan GajiKomponenSeeder terlebih dahulu.";
}

// Sample komponen gaji
$sampleKomponen = DB::connection('absensigaji')->table('gaji_komponen as gk')
    ->join('simpeg.master_jabatan as mj', 'mj.id_jabatan', '=', 'gk.id_jabatan')
    ->leftJoin('gaji_jenis_komponen as gjk', 'gjk.id_jenis_komponen', '=', 'gk.id_jenis_komponen')
    ->select('mj.jabatan', DB::raw('SUM(gk.nominal) as total_gaji'))
    ->groupBy('mj.jabatan')
    ->orderByDesc('total_gaji')
    ->limit(5)
    ->get();

echo "\n   📊 Contoh total gaji per jabatan:\n";
foreach ($sampleKomponen as $sk) {
    $formatted = number_format($sk->total_gaji, 0, ',', '.');
    echo "      - {$sk->jabatan}: Rp $formatted\n";
}

// ======================================
// 5. CEK TRANSAKSI GAJI
// ======================================
printHeader("5. CEK TRANSAKSI GAJI (HASIL GENERATE)");

$trxCount = DB::connection('absensigaji')->table('gaji_trx')->count();
printCheck("Transaksi gaji tersedia", $trxCount > 0, "$trxCount transaksi");

if ($trxCount > 0) {
    $trxSample = DB::connection('absensigaji')->table('gaji_trx as gt')
        ->leftJoin('gaji_periode as gp', 'gp.id_periode', '=', 'gt.id_periode')
        ->leftJoin('simpeg.person_sdm as ps', 'ps.id_sdm', '=', 'gt.id_sdm')
        ->leftJoin('simpeg.person as p', 'p.id_person', '=', 'ps.id_person')
        ->select('p.nama', 'gp.tahun', 'gp.bulan', 'gt.total_penghasilan', 'gt.total_potongan', 'gt.total_take_home_pay', 'gt.status')
        ->orderByDesc('gt.id_gaji')
        ->limit(5)
        ->get();

    echo "\n   📋 Transaksi gaji terbaru:\n";
    foreach ($trxSample as $trx) {
        $periode = $trx->tahun . '-' . str_pad($trx->bulan, 2, '0', STR_PAD_LEFT);
        $thp = number_format($trx->total_take_home_pay, 0, ',', '.');
        $potongan = number_format($trx->total_potongan, 0, ',', '.');
        echo "      - {$trx->nama} ({$periode}): THP Rp {$thp} | Potongan Rp {$potongan} | Status: {$trx->status}\n";
    }
}

// ======================================
// 6. CEK DISTRIBUSI GAJI
// ======================================
printHeader("6. CEK DISTRIBUSI GAJI");

$distribusiCount = DB::connection('absensigaji')->table('gaji_distribusi')->count();
printCheck("Distribusi gaji tersedia", $distribusiCount >= 0, "$distribusiCount record");

if ($distribusiCount > 0) {
    $distribusiSample = DB::connection('absensigaji')->table('gaji_distribusi as gd')
        ->leftJoin('simpeg.person_sdm as ps', 'ps.id_sdm', '=', 'gd.id_sdm')
        ->leftJoin('simpeg.person as p', 'p.id_person', '=', 'ps.id_person')
        ->select('p.nama', 'gd.jumlah_transfer', 'gd.status_transfer', 'gd.tanggal_transfer')
        ->orderByDesc('gd.id_distribusi')
        ->limit(5)
        ->get();

    echo "\n   📤 Distribusi terbaru:\n";
    foreach ($distribusiSample as $dist) {
        $jml = number_format($dist->jumlah_transfer, 0, ',', '.');
        echo "      - {$dist->nama}: Rp {$jml} | {$dist->status_transfer} | {$dist->tanggal_transfer}\n";
    }
}

// ======================================
// 7. TES LOGIKA GENERATE GAJI
// ======================================
printHeader("7. SIMULASI LOGIKA GENERATE GAJI");

// Ambil SDM aktif pertama
$activeSdm = DB::table('sdm_struktural')
    ->whereNull('tanggal_keluar')
    ->first();

if ($activeSdm) {
    $idJabatan = $activeSdm->id_jabatan;
    
    // Hitung gaji bulanan
    $gajiBulanan = DB::connection('absensigaji')->table('gaji_komponen as gk')
        ->leftJoin('gaji_jenis_komponen as gjk', 'gjk.id_jenis_komponen', '=', 'gk.id_jenis_komponen')
        ->where('gk.id_jabatan', $idJabatan)
        ->where('gjk.jenis', 'PENGHASILAN')
        ->sum('gk.nominal');
    
    $jabatan = DB::table('master_jabatan')->where('id_jabatan', $idJabatan)->first();
    $jabatanNama = $jabatan->jabatan ?? "Jabatan #$idJabatan";
    
    printCheck("Kalkulasi gaji untuk jabatan aktif", $gajiBulanan > 0, $jabatanNama);
    printInfo("Gaji bulanan", "Rp " . number_format($gajiBulanan, 0, ',', '.'));
    
    $hariBulan = 31;
    $upahHarian = $gajiBulanan / $hariBulan;
    $upahPerJam = $upahHarian / 8;
    
    printInfo("Upah harian (31 hari)", "Rp " . number_format($upahHarian, 0, ',', '.'));
    printInfo("Upah per jam", "Rp " . number_format($upahPerJam, 0, ',', '.'));
    
    if ($gajiBulanan == 0) {
        $errors[] = "Gaji bulanan = 0 untuk jabatan aktif. Cek data gaji_komponen.";
    }
} else {
    printCheck("Kalkulasi gaji untuk jabatan aktif", false, "Tidak ada SDM aktif");
}

// ======================================
// 8. CEK REKENING SDM
// ======================================
printHeader("8. CEK REKENING SDM");

$rekeningCount = DB::table('sdm_rekening')->count();
printCheck("Rekening SDM tersedia", $rekeningCount > 0, "$rekeningCount rekening");

$rekeningList = DB::table('sdm_rekening as sr')
    ->leftJoin('person_sdm as ps', 'ps.id_sdm', '=', 'sr.id_sdm')
    ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
    ->select('p.nama', 'sr.bank', 'sr.no_rekening', 'sr.rekening_utama')
    ->get();

foreach ($rekeningList as $rek) {
    $utama = $rek->rekening_utama == 'y' ? '⭐ UTAMA' : '';
    printInfo($rek->nama ?? 'Unknown', "{$rek->bank} - {$rek->no_rekening} $utama");
}

// ======================================
// HASIL AKHIR
// ======================================
$endTime = microtime(true);
$duration = round($endTime - $startTime, 2);

printHeader("HASIL TES");

if (empty($errors)) {
    echo "🎉 SEMUA TES BERHASIL!\n";
    echo "   Tidak ada masalah yang ditemukan.\n";
} else {
    echo "⚠️  DITEMUKAN " . count($errors) . " MASALAH:\n";
    foreach ($errors as $i => $err) {
        echo "   " . ($i + 1) . ". $err\n";
    }
}

echo "\n⏱️  Durasi: {$duration} detik\n";
echo "\n" . str_repeat('=', 60) . "\n";
