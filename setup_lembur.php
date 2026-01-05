<?php
/**
 * Setup Overtime Feature
 * Creates tarif_lembur table, adds total_lembur to absensi, seeds hari_libur
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== SETUP OVERTIME FEATURE ===\n\n";

// 1. Create tarif_lembur table
echo "1. Creating tarif_lembur table...\n";
try {
    if (!Schema::connection('mysql')->hasTable('tarif_lembur')) {
        DB::connection('mysql')->statement("
            CREATE TABLE tarif_lembur (
                id_tarif INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama_tarif VARCHAR(100) NOT NULL,
                tarif_per_jam DECIMAL(15,2) NOT NULL DEFAULT 0,
                keterangan TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "   ✅ Table tarif_lembur created\n";
    } else {
        echo "   ℹ️ Table tarif_lembur already exists\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 2. Add total_lembur column to absensi
echo "\n2. Adding total_lembur column to absensi...\n";
try {
    if (!Schema::connection('mysql')->hasColumn('absensi', 'total_lembur')) {
        DB::connection('mysql')->statement("
            ALTER TABLE absensi ADD COLUMN total_lembur DECIMAL(8,2) DEFAULT 0 AFTER total_pulang_awal
        ");
        echo "   ✅ Column total_lembur added to absensi\n";
    } else {
        echo "   ℹ️ Column total_lembur already exists\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 3. Insert default tarif lembur
echo "\n3. Inserting default tarif lembur...\n";
try {
    $exists = DB::connection('mysql')->table('tarif_lembur')->count();
    if ($exists == 0) {
        DB::connection('mysql')->table('tarif_lembur')->insert([
            [
                'nama_tarif' => 'Lembur Biasa',
                'tarif_per_jam' => 50000,
                'keterangan' => 'Lembur di hari kerja biasa'
            ],
            [
                'nama_tarif' => 'Lembur Libur',
                'tarif_per_jam' => 75000,
                'keterangan' => 'Lembur di hari libur nasional atau akhir pekan'
            ]
        ]);
        echo "   ✅ Inserted 2 default tarif lembur\n";
    } else {
        echo "   ℹ️ Tarif lembur data already exists ({$exists} records)\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 4. Seed hari libur (Sundays in January 2026)
echo "\n4. Seeding hari libur (Sundays in January 2026)...\n";
try {
    $sundays = [
        ['tanggal' => '2026-01-04', 'nama' => 'Hari Minggu', 'keterangan' => 'Libur mingguan'],
        ['tanggal' => '2026-01-11', 'nama' => 'Hari Minggu', 'keterangan' => 'Libur mingguan'],
        ['tanggal' => '2026-01-18', 'nama' => 'Hari Minggu', 'keterangan' => 'Libur mingguan'],
        ['tanggal' => '2026-01-25', 'nama' => 'Hari Minggu', 'keterangan' => 'Libur mingguan'],
    ];
    
    $inserted = 0;
    foreach ($sundays as $sunday) {
        $exists = DB::connection('mysql')->table('hari_libur')
            ->where('tanggal', $sunday['tanggal'])
            ->exists();
        
        if (!$exists) {
            DB::connection('mysql')->table('hari_libur')->insert($sunday);
            $inserted++;
        }
    }
    
    if ($inserted > 0) {
        echo "   ✅ Inserted {$inserted} hari libur records\n";
    } else {
        echo "   ℹ️ Hari libur data already exists\n";
    }
    
    // Show all hari libur
    $allLibur = DB::connection('mysql')->table('hari_libur')->get();
    echo "\n   📅 Data Hari Libur:\n";
    foreach ($allLibur as $hl) {
        echo "      - {$hl->tanggal}: {$hl->nama}\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== SETUP COMPLETE ===\n";
