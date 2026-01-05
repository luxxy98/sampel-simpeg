<?php
/**
 * Setup Integration: Hari Libur & Tarif Lembur dengan Absensi
 * 
 * Script ini menambahkan kolom baru ke tabel absensi untuk integrasi
 * dengan fitur hari libur dan tarif lembur.
 * 
 * AMAN: Menggunakan cek hasColumn() sebelum ALTER
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== SETUP INTEGRATION: Hari Libur & Tarif Lembur ===\n\n";

// 1. Add is_hari_libur column to absensi
echo "1. Adding is_hari_libur column to absensi...\n";
try {
    if (!Schema::connection('mysql')->hasColumn('absensi', 'is_hari_libur')) {
        DB::connection('mysql')->statement("
            ALTER TABLE absensi ADD COLUMN is_hari_libur TINYINT(1) DEFAULT 0 AFTER total_lembur
        ");
        echo "   ✅ Column is_hari_libur added\n";
    } else {
        echo "   ℹ️ Column is_hari_libur already exists\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 2. Add id_tarif_lembur column to absensi
echo "\n2. Adding id_tarif_lembur column to absensi...\n";
try {
    if (!Schema::connection('mysql')->hasColumn('absensi', 'id_tarif_lembur')) {
        DB::connection('mysql')->statement("
            ALTER TABLE absensi ADD COLUMN id_tarif_lembur INT UNSIGNED NULL AFTER is_hari_libur
        ");
        echo "   ✅ Column id_tarif_lembur added\n";
    } else {
        echo "   ℹ️ Column id_tarif_lembur already exists\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 3. Add nominal_lembur column to absensi
echo "\n3. Adding nominal_lembur column to absensi...\n";
try {
    if (!Schema::connection('mysql')->hasColumn('absensi', 'nominal_lembur')) {
        DB::connection('mysql')->statement("
            ALTER TABLE absensi ADD COLUMN nominal_lembur DECIMAL(15,2) DEFAULT 0 AFTER id_tarif_lembur
        ");
        echo "   ✅ Column nominal_lembur added\n";
    } else {
        echo "   ℹ️ Column nominal_lembur already exists\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 4. Verify columns
echo "\n4. Verifying columns...\n";
$columns = Schema::connection('mysql')->getColumnListing('absensi');
echo "   Columns in absensi table: " . implode(', ', $columns) . "\n";

// 5. Check tarif_lembur data
echo "\n5. Checking tarif_lembur data...\n";
try {
    $tarifCount = DB::connection('mysql')->table('tarif_lembur')->count();
    echo "   Found {$tarifCount} tarif lembur records\n";
    
    if ($tarifCount > 0) {
        $tarifs = DB::connection('mysql')->table('tarif_lembur')->get();
        foreach ($tarifs as $t) {
            echo "   - ID {$t->id_tarif}: {$t->nama_tarif} = Rp " . number_format($t->tarif_per_jam, 0, ',', '.') . "/jam\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 6. Check hari_libur data
echo "\n6. Checking hari_libur data...\n";
try {
    $liburCount = DB::connection('mysql')->table('hari_libur')->count();
    echo "   Found {$liburCount} hari libur records\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SETUP COMPLETE ===\n";
echo "\nNext steps:\n";
echo "1. Update Absensi model with new fields\n";
echo "2. Update AbsensiService with integration logic\n";
echo "3. Update views with UI indicators\n";
