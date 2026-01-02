<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GajiKomponenSeeder extends Seeder
{
    public function run(): void
    {
        $jabatanList = DB::table('master_jabatan')->select('id_jabatan', 'jabatan')->get();
        $jenisKomponen = DB::connection('absensigaji')->table('gaji_jenis_komponen')
            ->where('jenis', 'PENGHASILAN')
            ->get();

        if ($jenisKomponen->isEmpty()) {
            $this->command->info('Tidak ada jenis komponen PENGHASILAN');
            return;
        }

        $insertData = [];
        foreach ($jabatanList as $jab) {
            $nama = strtolower($jab->jabatan);
            
            // Level 1: Pimpinan (Rektor, Wakil Rektor, Direktur Utama)
            if (str_contains($nama, 'rektor') || str_contains($nama, 'direktur utama')) {
                $gajiPokok = 15000000;
                $tunjangan = 5000000;
                $transport = 2000000;
            }
            // Level 2: Dekan, Direktur
            elseif (str_contains($nama, 'dekan') || str_contains($nama, 'direktur')) {
                $gajiPokok = 12000000;
                $tunjangan = 3500000;
                $transport = 1500000;
            }
            // Level 3: Kepala (Lembaga, Biro, Pusat, LAE, etc)
            elseif (str_contains($nama, 'kepala')) {
                $gajiPokok = 10000000;
                $tunjangan = 3000000;
                $transport = 1200000;
            }
            // Level 4: Ketua Prodi, Kabid
            elseif (str_contains($nama, 'ketua prodi') || str_contains($nama, 'kabid') || str_contains($nama, 'ka. bidang') || str_contains($nama, 'ka. sub')) {
                $gajiPokok = 8000000;
                $tunjangan = 2500000;
                $transport = 1000000;
            }
            // Level 5: Wakil, Sekretaris
            elseif (str_contains($nama, 'wakil') || str_contains($nama, 'sekretaris')) {
                $gajiPokok = 9000000;
                $tunjangan = 2800000;
                $transport = 1100000;
            }
            // Level 6: Staf & lainnya
            else {
                $gajiPokok = 5000000;
                $tunjangan = 1500000;
                $transport = 500000;
            }

            foreach ($jenisKomponen as $jk) {
                $nominal = match($jk->id_jenis_komponen) {
                    1 => $gajiPokok,
                    2 => $tunjangan,
                    3 => $transport,
                    default => 0,
                };
                
                $insertData[] = [
                    'id_jabatan' => $jab->id_jabatan,
                    'id_jenis_komponen' => $jk->id_jenis_komponen,
                    'nominal' => $nominal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Hapus data lama dan insert baru
        DB::connection('absensigaji')->table('gaji_komponen')->truncate();
        DB::connection('absensigaji')->table('gaji_komponen')->insert($insertData);

        $this->command->info('Berhasil insert ' . count($insertData) . ' komponen gaji untuk ' . count($jabatanList) . ' jabatan');
    }
}
