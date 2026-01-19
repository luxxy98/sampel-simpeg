<?php

namespace App\Services\Gaji;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Gaji\GajiTrx;
use App\Models\Gaji\GajiDetail;
use App\Models\Gaji\GajiPeriode;

final class GajiGenerateService
{
    private string $mainDb;

    public function __construct()
    {
        $mainConn = (string) config('database.default', 'mysql');
        $this->mainDb = (string) config("database.connections.{$mainConn}.database");
    }

    /**
     * Generate gaji untuk semua pegawai aktif dalam periode tertentu
     */
    public function generateForPeriode(int $idPeriode): array
    {
        $periode = GajiPeriode::find($idPeriode);
        if (!$periode) {
            throw new \Exception('Periode tidak ditemukan');
        }

        $startDate = Carbon::parse($periode->tanggal_mulai);
        $endDate = Carbon::parse($periode->tanggal_selesai);
        $hariBulan = $startDate->daysInMonth;

        // Ambil semua pegawai aktif dengan jabatan terakhir
        $pegawaiList = $this->getActivePegawaiWithJabatan();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($pegawaiList as $pegawai) {
            try {
                $this->generateForPegawai($idPeriode, $pegawai, $startDate, $endDate, $hariBulan);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "SDM #{$pegawai->id_sdm}: " . $e->getMessage();
            }
        }

        return [
            'total_pegawai' => count($pegawaiList),
            'success' => $successCount,
            'error' => $errorCount,
            'errors' => $errors,
        ];
    }

    /**
     * Ambil pegawai aktif dengan jabatan terakhir dari sdm_struktural
     */
    private function getActivePegawaiWithJabatan(): Collection
    {
        return DB::connection('mysql')->table('person_sdm as ps')
            ->join('sdm_struktural as ss', function ($join) {
                $join->on('ss.id_sdm', '=', 'ps.id_sdm')
                    ->whereNull('ss.tanggal_keluar'); // Masih aktif
            })
            ->select([
                'ps.id_sdm',
                'ss.id_jabatan',
            ])
            ->groupBy('ps.id_sdm', 'ss.id_jabatan')
            ->get();
    }

    /**
     * Hitung gaji bulanan berdasar komponen PENGHASILAN untuk jabatan
     */
    private function getGajiBulanan(int $idJabatan): float
    {
        $total = DB::connection('absensigaji')->table('gaji_komponen as gk')
            ->join('gaji_jenis_komponen as gjk', 'gjk.id_jenis_komponen', '=', 'gk.id_jenis_komponen')
            ->where('gk.id_jabatan', $idJabatan)
            ->where('gjk.jenis', 'PENGHASILAN')
            ->sum('gk.nominal');

        return (float) ($total ?? 0);
    }

    /**
     * Ambil komponen penghasilan per jabatan untuk detail
     */
    private function getKomponenPenghasilan(int $idJabatan): Collection
    {
        return DB::connection('absensigaji')->table('gaji_komponen as gk')
            ->join('gaji_jenis_komponen as gjk', 'gjk.id_jenis_komponen', '=', 'gk.id_jenis_komponen')
            ->where('gk.id_jabatan', $idJabatan)
            ->where('gjk.jenis', 'PENGHASILAN')
            ->select(['gk.id_gaji_komponen', 'gk.nominal', 'gjk.nama_komponen'])
            ->get();
    }

    /**
     * Generate gaji untuk 1 pegawai
     */
    private function generateForPegawai(
        int $idPeriode,
        object $pegawai,
        Carbon $startDate,
        Carbon $endDate,
        int $hariBulan
    ): void {
        $idSdm = $pegawai->id_sdm;
        $idJabatan = $pegawai->id_jabatan;

        // 1. Hitung gaji bulanan
        $gajiBulanan = $this->getGajiBulanan($idJabatan);
        $upahHarian = $hariBulan > 0 ? $gajiBulanan / $hariBulan : 0;
        $upahPerJam = $upahHarian / 8;

        // 2. Hitung potongan harian (ALPHA + CUTI)
        $potonganHarian = $this->hitungPotonganHarian($idSdm, $startDate, $endDate, $upahHarian);

        // 3. Hitung potongan telat (exclude tanggal ALPHA/CUTI)
        $potonganTelat = $this->hitungPotonganTelat($idSdm, $startDate, $endDate, $upahPerJam);

        // 3.5 Hitung Lembur dari absensi.nominal_lembur
        $lembur = $this->hitungLembur($idSdm, $startDate, $endDate);

        // 4. Total (termasuk lembur)
        $totalPotongan = $potonganHarian['total'] + $potonganTelat['total'];
        $totalPenghasilan = $gajiBulanan + $lembur['nominal'];
        $takeHomePay = $totalPenghasilan - $totalPotongan;

        // 5. Simpan/Update gaji_trx
        $gajiTrx = GajiTrx::updateOrCreate(
            ['id_periode' => $idPeriode, 'id_sdm' => $idSdm],
            [
                'total_penghasilan' => $totalPenghasilan,
                'total_potongan' => $totalPotongan,
                'total_take_home_pay' => $takeHomePay,
                'status' => 'DRAFT',
            ]
        );

        // 6. Hapus detail lama & insert ulang
        GajiDetail::where('id_gaji', $gajiTrx->id_gaji)->delete();

        // Insert detail penghasilan per komponen
        $komponenPenghasilan = $this->getKomponenPenghasilan($idJabatan);
        foreach ($komponenPenghasilan as $kp) {
            GajiDetail::create([
                'id_gaji' => $gajiTrx->id_gaji,
                'id_gaji_komponen' => $kp->id_gaji_komponen,
                'nominal' => $kp->nominal,
                'keterangan' => "Penghasilan: {$kp->nama_komponen}",
            ]);
        }

        // Insert detail potongan
        if ($potonganHarian['hari_alpha'] > 0) {
            GajiDetail::create([
                'id_gaji' => $gajiTrx->id_gaji,
                'id_gaji_komponen' => null,
                'nominal' => -($potonganHarian['hari_alpha'] * $upahHarian),
                'keterangan' => "Potongan ALPHA: {$potonganHarian['hari_alpha']} hari x Rp " . number_format($upahHarian, 0, ',', '.'),
            ]);
        }

        if ($potonganHarian['hari_cuti'] > 0) {
            GajiDetail::create([
                'id_gaji' => $gajiTrx->id_gaji,
                'id_gaji_komponen' => null,
                'nominal' => -($potonganHarian['hari_cuti'] * $upahHarian),
                'keterangan' => "Potongan CUTI: {$potonganHarian['hari_cuti']} hari x Rp " . number_format($upahHarian, 0, ',', '.'),
            ]);
        }

        if ($potonganTelat['jam_telat'] > 0) {
            GajiDetail::create([
                'id_gaji' => $gajiTrx->id_gaji,
                'id_gaji_komponen' => null,
                'nominal' => -$potonganTelat['total'],
                'keterangan' => "Potongan TELAT: {$potonganTelat['jam_telat']} jam x Rp " . number_format($upahPerJam, 0, ',', '.'),
            ]);
        }

        // Insert detail lembur (penghasilan tambahan)
        if ($lembur['nominal'] > 0) {
            GajiDetail::create([
                'id_gaji' => $gajiTrx->id_gaji,
                'id_gaji_komponen' => null,
                'nominal' => $lembur['nominal'],
                'keterangan' => "Uang Lembur: " . number_format($lembur['jam_lembur'], 2) . " jam = Rp " . number_format($lembur['nominal'], 0, ',', '.'),
            ]);
        }
    }

    /**
     * Hitung potongan harian (ALPHA dari absensi + CUTI dari cuti_pengajuan disetujui)
     */
    private function hitungPotonganHarian(int $idSdm, Carbon $startDate, Carbon $endDate, float $upahHarian): array
    {
        // Ambil ID jenis absen ALPHA
        $jenisAlpha = DB::connection('mysql')->table('absen_jenis')
            ->where('nama_absen', 'ALPHA')->value('id_jenis_absen');

        // Hitung hari ALPHA (tanggal unik dari absensi)
        $hariAlpha = 0;
        if ($jenisAlpha) {
            $hariAlpha = DB::connection('mysql')->table('absensi as a')
                ->join('absensi_detail as ad', 'ad.id_absensi', '=', 'a.id_absensi')
                ->where('a.id_sdm', $idSdm)
                ->whereBetween('a.tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('a.is_hari_libur', 0)
                ->where('ad.id_jenis_absen', $jenisAlpha)
                ->distinct('a.tanggal')
                ->count('a.tanggal');
        }

        // Hitung hari CUTI dari cuti_pengajuan yang DISETUJUI
        // Query cuti yang overlap dengan periode gaji
        $cutiList = DB::connection('mysql')->table('cuti_pengajuan')
            ->where('id_sdm', $idSdm)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($startDate, $endDate) {
                // Cuti yang overlap dengan periode gaji
                $q->whereBetween('tanggal_mulai', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('tanggal_selesai', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      // Cuti yang mencakup seluruh periode
                      $q2->where('tanggal_mulai', '<=', $startDate->format('Y-m-d'))
                         ->where('tanggal_selesai', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get(['tanggal_mulai', 'tanggal_selesai']);

        // Hitung jumlah hari cuti yang jatuh dalam periode gaji
        $hariCuti = 0;
        foreach ($cutiList as $cuti) {
            $cutiMulai = Carbon::parse($cuti->tanggal_mulai);
            $cutiSelesai = Carbon::parse($cuti->tanggal_selesai);
            
            // Clamp ke periode gaji
            $effectiveStart = $cutiMulai->lt($startDate) ? $startDate->copy() : $cutiMulai->copy();
            $effectiveEnd = $cutiSelesai->gt($endDate) ? $endDate->copy() : $cutiSelesai->copy();
            
            // Hitung jumlah hari (inclusive)
            if ($effectiveStart->lte($effectiveEnd)) {
                $hariCuti += $effectiveStart->diffInDays($effectiveEnd) + 1;
            }
        }

        $total = ($hariAlpha + $hariCuti) * $upahHarian;

        return [
            'hari_alpha' => $hariAlpha,
            'hari_cuti' => $hariCuti,
            'total' => $total,
        ];
    }

    /**
     * Hitung potongan telat (exclude tanggal ALPHA/CUTI)
     */
    private function hitungPotonganTelat(int $idSdm, Carbon $startDate, Carbon $endDate, float $upahPerJam): array
    {
        // Ambil tanggal yang sudah ALPHA
        $jenisAlpha = DB::connection('mysql')->table('absen_jenis')
            ->where('nama_absen', 'ALPHA')->value('id_jenis_absen');

        // Tanggal ALPHA dari absensi
        $excludeDates = [];
        if ($jenisAlpha) {
            $excludeDates = DB::connection('mysql')->table('absensi as a')
                ->join('absensi_detail as ad', 'ad.id_absensi', '=', 'a.id_absensi')
                ->where('a.id_sdm', $idSdm)
                ->whereBetween('a.tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('a.is_hari_libur', 0)
                ->where('ad.id_jenis_absen', $jenisAlpha)
                ->pluck('a.tanggal')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->unique()
                ->toArray();
        }

        // Tambahkan tanggal cuti dari cuti_pengajuan yang disetujui
        $cutiList = DB::connection('mysql')->table('cuti_pengajuan')
            ->where('id_sdm', $idSdm)
            ->where('status', 'disetujui')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_mulai', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('tanggal_selesai', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('tanggal_mulai', '<=', $startDate->format('Y-m-d'))
                         ->where('tanggal_selesai', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get(['tanggal_mulai', 'tanggal_selesai']);

        foreach ($cutiList as $cuti) {
            $cutiMulai = Carbon::parse($cuti->tanggal_mulai);
            $cutiSelesai = Carbon::parse($cuti->tanggal_selesai);
            
            // Clamp ke periode gaji
            $effectiveStart = $cutiMulai->lt($startDate) ? $startDate->copy() : $cutiMulai->copy();
            $effectiveEnd = $cutiSelesai->gt($endDate) ? $endDate->copy() : $cutiSelesai->copy();
            
            // Tambahkan semua tanggal cuti ke excludeDates
            $current = $effectiveStart->copy();
            while ($current->lte($effectiveEnd)) {
                $excludeDates[] = $current->format('Y-m-d');
                $current->addDay();
            }
        }
        $excludeDates = array_unique($excludeDates);

        // Hitung total jam telat, exclude tanggal ALPHA/CUTI
        $query = DB::connection('mysql')->table('absensi')
            ->where('id_sdm', $idSdm)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        // Hari libur tidak ada potongan telat
        $query->where('is_hari_libur', 0);

        if (!empty($excludeDates)) {
            $query->whereNotIn('tanggal', $excludeDates);
        }

        $jamTelat = (float) $query->sum('total_terlambat');

        $total = $jamTelat * $upahPerJam;

        return [
            'jam_telat' => $jamTelat,
            'total' => $total,
        ];
    }

    /**
     * Hitung total lembur dari absensi.nominal_lembur
     */
    private function hitungLembur(int $idSdm, Carbon $startDate, Carbon $endDate): array
    {
        // Total jam lembur
        $totalJam = DB::connection('mysql')->table('absensi')
            ->where('id_sdm', $idSdm)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('total_lembur');

        // Total nominal lembur (sudah dihitung saat input absensi)
        $totalNominal = DB::connection('mysql')->table('absensi')
            ->where('id_sdm', $idSdm)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('nominal_lembur');

        return [
            'jam_lembur' => (float) ($totalJam ?? 0),
            'nominal' => (float) ($totalNominal ?? 0),
        ];
    }
}
