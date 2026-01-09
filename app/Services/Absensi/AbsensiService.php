<?php

namespace App\Services\Absensi;

use App\Models\Absensi\Absensi;
use App\Models\Absensi\AbsensiDetail;
use App\Models\Absensi\AbsenJenis;
use App\Models\Absensi\JadwalKaryawan;
use App\Models\Gaji\TarifLembur;
use App\Models\Ref\RefLiburNasional;
use App\Models\Ref\RefLiburPt;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AbsensiService
{
    /**
     * Get list query for DataTable
     */
    public function listQuery(?string $tanggalMulai = null, ?string $tanggalSelesai = null, ?int $idSdm = null): Builder
    {
        $q = Absensi::query()
            ->select([
                'id_absensi',
                'tanggal',
                'id_sdm',
                'id_jadwal_karyawan',
                'total_jam_kerja',
                'total_terlambat',
                'total_pulang_awal',
                'total_lembur',
                'is_hari_libur',
                'nominal_lembur',
            ]);

        if ($tanggalMulai) $q->whereDate('tanggal', '>=', $tanggalMulai);
        if ($tanggalSelesai) $q->whereDate('tanggal', '<=', $tanggalSelesai);
        if ($idSdm) $q->where('id_sdm', $idSdm);

        return $q;
    }

    public function find(int $id): ?Absensi
    {
        return Absensi::query()->whereKey($id)->first();
    }

    public function getShowBundle(int $id): ?array
    {
        $absensi = $this->find($id);
        if (!$absensi) return null;

        $abs = $absensi->toArray();
        $abs['sdm_nama'] = $this->getSdmName((int) $absensi->id_sdm) ?? ('SDM #' . $absensi->id_sdm);
        $abs['jadwal_nama'] = $this->getJadwalName((int) $absensi->id_jadwal_karyawan) ?? ('Jadwal #' . $absensi->id_jadwal_karyawan);
        
        // Get tarif lembur name if exists
        if ($absensi->id_tarif_lembur) {
            $tarif = TarifLembur::find($absensi->id_tarif_lembur);
            $abs['tarif_lembur_nama'] = $tarif?->nama_tarif ?? '-';
            $abs['tarif_per_jam'] = $tarif?->tarif_per_jam ?? 0;
        } else {
            $abs['tarif_lembur_nama'] = '-';
            $abs['tarif_per_jam'] = 0;
        }

        $detail = DB::connection('mysql')->table('absensi_detail as ad')
            ->leftJoin('absen_jenis as aj', 'aj.id_jenis_absen', '=', 'ad.id_jenis_absen')
            ->where('ad.id_absensi', $id)
            ->orderBy('ad.id_detail')
            ->get([
                'ad.id_detail',
                'ad.id_absensi',
                'ad.id_jenis_absen',
                'aj.nama_absen',
                'aj.kategori',
                'ad.waktu_mulai',
                'ad.waktu_selesai',
                'ad.durasi_jam',
                'ad.lokasi_pulang',
            ])
            ->map(fn ($r) => (array) $r)
            ->all();

        return ['absensi' => $abs, 'detail' => $detail];
    }

    /**
     * Create absensi with automatic holiday detection and overtime calculation
     */
    public function create(array $data, array $detailRows): int
    {
        // Check if date is a holiday
        $isHariLibur = $this->isHoliday($data['tanggal']);
        
        // Get appropriate tarif lembur based on holiday status
        $tarifLembur = $this->getTarifLembur($isHariLibur);
        $idTarifLembur = $tarifLembur?->id_tarif ?? null;
        $tarifPerJam = $tarifLembur?->tarif_per_jam ?? 0;
        
        // Calculate nominal lembur
        $totalLembur = (float) ($data['total_lembur'] ?? 0);
        $nominalLembur = $totalLembur * $tarifPerJam;

        $absensi = Absensi::create([
            'tanggal' => $data['tanggal'],
            'id_jadwal_karyawan' => $data['id_jadwal_karyawan'],
            'id_sdm' => $data['id_sdm'],
            'total_jam_kerja' => $data['total_jam_kerja'] ?? 0,
            'total_terlambat' => $data['total_terlambat'] ?? 0,
            'total_pulang_awal' => $data['total_pulang_awal'] ?? 0,
            'total_lembur' => $totalLembur,
            'is_hari_libur' => $isHariLibur ? 1 : 0,
            'id_tarif_lembur' => $idTarifLembur,
            'nominal_lembur' => $nominalLembur,
        ]);

        foreach ($detailRows as $row) {
            $row['id_absensi'] = (int) $absensi->getKey();
            AbsensiDetail::create($row);
        }

        return (int) $absensi->getKey();
    }

    /**
     * Update absensi with automatic holiday detection and overtime calculation
     */
    public function update(Absensi $absensi, array $data, array $detailRows): void
    {
        // Check if date is a holiday
        $isHariLibur = $this->isHoliday($data['tanggal']);
        
        // Get appropriate tarif lembur based on holiday status
        $tarifLembur = $this->getTarifLembur($isHariLibur);
        $idTarifLembur = $tarifLembur?->id_tarif ?? null;
        $tarifPerJam = $tarifLembur?->tarif_per_jam ?? 0;
        
        // Calculate nominal lembur
        $totalLembur = (float) ($data['total_lembur'] ?? 0);
        $nominalLembur = $totalLembur * $tarifPerJam;

        $absensi->update([
            'tanggal' => $data['tanggal'],
            'id_jadwal_karyawan' => $data['id_jadwal_karyawan'],
            'id_sdm' => $data['id_sdm'],
            'total_jam_kerja' => $data['total_jam_kerja'] ?? 0,
            'total_terlambat' => $data['total_terlambat'] ?? 0,
            'total_pulang_awal' => $data['total_pulang_awal'] ?? 0,
            'total_lembur' => $totalLembur,
            'is_hari_libur' => $isHariLibur ? 1 : 0,
            'id_tarif_lembur' => $idTarifLembur,
            'nominal_lembur' => $nominalLembur,
        ]);

        AbsensiDetail::query()->where('id_absensi', (int) $absensi->getKey())->delete();

        foreach ($detailRows as $row) {
            $row['id_absensi'] = (int) $absensi->getKey();
            AbsensiDetail::create($row);
        }
    }

    public function delete(Absensi $absensi): void
    {
        AbsensiDetail::query()->where('id_absensi', (int) $absensi->getKey())->delete();
        $absensi->delete();
    }

    /**
     * Check if a date is a holiday.
     * Sumber libur:
     * - Hari Minggu (weekly off)
     * - ref_libur_nasional
     * - ref_libur_pt
     */
    public function isHoliday(string $date): bool
    {
        // Minggu
        if ((int) date('w', strtotime($date)) === 0) {
            return true;
        }

        // Libur Nasional / Libur PT
        return RefLiburNasional::whereDate('tanggal', $date)->exists()
            || RefLiburPt::whereDate('tanggal', $date)->exists();
    }

    /**
     * Get appropriate tarif lembur based on holiday status
     * Returns "Lembur Libur" for holidays, "Lembur Biasa" for regular days
     */
    public function getTarifLembur(bool $isHariLibur): ?TarifLembur
    {
        if ($isHariLibur) {
            // Try to find "Lembur Libur" tarif
            $tarif = TarifLembur::where('nama_tarif', 'LIKE', '%Libur%')->first();
            if ($tarif) return $tarif;
        }
        
        // Default to "Lembur Biasa" or first available tarif
        $tarif = TarifLembur::where('nama_tarif', 'LIKE', '%Biasa%')->first();
        if ($tarif) return $tarif;
        
        // Fallback to first tarif
        return TarifLembur::first();
    }

    /**
     * Get holiday info for a specific date (for AJAX)
     */
    public function getHolidayInfo(string $date): array
    {
        $isHoliday = $this->isHoliday($date);
        $tarif = $this->getTarifLembur($isHoliday);
        
        $holidayName = null;
        $holidayType = null;
        if ($isHoliday) {
            $dayOfWeek = (int) date('w', strtotime($date));
            if ($dayOfWeek === 0) {
                $holidayName = 'Hari Minggu';
                $holidayType = 'MINGGU';
            } else {
                $nasional = RefLiburNasional::whereDate('tanggal', $date)->first();
                if ($nasional) {
                    // Kolom yang tersedia di tabel ref_libur_nasional pada project ini: tanggal, keterangan
                    $holidayName = $nasional->keterangan ?? 'Libur Nasional';
                    $holidayType = 'NASIONAL';
                } else {
                    $pt = RefLiburPt::whereDate('tanggal', $date)->first();
                    if ($pt) {
                        $holidayName = $pt->keterangan ?? 'Libur PT';
                        $holidayType = 'PT';
                    } else {
                        $holidayName = 'Hari Libur';
                        $holidayType = 'UNKNOWN';
                    }
                }
            }
        }
        
        return [
            'is_hari_libur' => $isHoliday,
            'holiday_name' => $holidayName,
            'holiday_type' => $holidayType,
            'id_tarif_lembur' => $tarif?->id_tarif,
            'nama_tarif' => $tarif?->nama_tarif,
            'tarif_per_jam' => $tarif?->tarif_per_jam ?? 0,
        ];
    }

    /**
     * Get all holidays for calendar/reference
     */
    public function getHolidayDates(): Collection
    {
        $nasional = RefLiburNasional::query()->pluck('tanggal')->map(fn($d) => $d->format('Y-m-d'));
        $pt = RefLiburPt::query()->pluck('tanggal')->map(fn($d) => $d->format('Y-m-d'));

        return $nasional
            ->merge($pt)
            ->unique()
            ->values();
    }

    /**
     * Get tarif lembur options for dropdown
     */
    public function tarifLemburOptions(): Collection
    {
        return TarifLembur::query()
            ->select(['id_tarif', 'nama_tarif', 'tarif_per_jam'])
            ->orderBy('nama_tarif')
            ->get();
    }

    public function normalizeDetailRows(array $detail): array
    {
        if (empty($detail)) return [];

        $keys = ['id_jenis_absen', 'waktu_mulai', 'waktu_selesai', 'durasi_jam', 'lokasi_pulang'];
        $n = 0;
        foreach ($keys as $k) $n = max($n, is_array($detail[$k] ?? null) ? count($detail[$k]) : 0);

        $rows = [];
        for ($i = 0; $i < $n; $i++) {
            $idJenis = Arr::get($detail, "id_jenis_absen.$i");
            if (empty($idJenis)) continue;

            $rows[] = [
                'id_jenis_absen' => (int) $idJenis,
                'waktu_mulai' => Arr::get($detail, "waktu_mulai.$i"),
                'waktu_selesai' => Arr::get($detail, "waktu_selesai.$i"),
                'durasi_jam' => (float) (Arr::get($detail, "durasi_jam.$i") ?? 0),
                'lokasi_pulang' => Arr::get($detail, "lokasi_pulang.$i"),
            ];
        }

        return $rows;
    }

    public function sdmOptions(): Collection
    {
        return DB::table('person_sdm as ps')
            ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
            ->select(['ps.id_sdm', DB::raw('p.nama as nama')])
            ->orderBy('p.nama')
            ->get();
    }

    public function jenisAbsenOptions(): Collection
    {
        return AbsenJenis::query()
            ->select(['id_jenis_absen', 'nama_absen'])
            ->orderBy('kategori')
            ->orderBy('nama_absen')
            ->get();
    }

    public function jadwalOptions(): Collection
    {
        try {
            return JadwalKaryawan::query()
                ->select(['id_jadwal_karyawan', DB::raw('nama_jadwal as nama'), 'jam_masuk', 'jam_pulang'])
                ->orderBy('nama_jadwal')
                ->get()
                ->map(fn ($r) => $r->toArray());
        } catch (Throwable) {
            return collect([['id_jadwal_karyawan' => 1, 'nama' => 'Default (#1)', 'jam_masuk' => '07:00:00', 'jam_pulang' => '15:00:00']]);
        }
    }

    public function jadwalKaryawanOptionsForDate(int $idSdm, string $tanggal): array
{
    return DB::connection('mysql')
        ->table('sdm_jadwal_karyawan as sjk')
        ->join('master_jadwal_kerja as mjk', 'mjk.id_jadwal', '=', 'sjk.id_jadwal')
        ->where('sjk.id_sdm', $idSdm)
        ->whereDate('sjk.tanggal_mulai', '<=', $tanggal)
        ->whereDate('sjk.tanggal_selesai', '>=', $tanggal)
        ->orderByDesc('sjk.tanggal_mulai')
        ->get([
            'sjk.id_jadwal_karyawan',
            'sjk.id_jadwal',
            'sjk.tanggal_mulai',
            'sjk.tanggal_selesai',
            'mjk.nama_jadwal',
            'mjk.jam_masuk',
            'mjk.jam_pulang',
        ])
        ->map(fn($r) => (array) $r)
        ->all();
}


    private function getSdmName(int $idSdm): ?string
    {
        $row = DB::table('person_sdm as ps')
            ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
            ->where('ps.id_sdm', $idSdm)
            ->select([DB::raw('p.nama as nama')])
            ->first();

        return $row?->nama;
    }

    private function getJadwalName(int $idJadwal): ?string
    {
        try {
            $row = JadwalKaryawan::query()
                ->select(['nama_jadwal'])
                ->where('id_jadwal_karyawan', $idJadwal)
                ->first();

            return $row?->nama_jadwal;
        } catch (Throwable) {
            return null;
        }
    }
    
}

