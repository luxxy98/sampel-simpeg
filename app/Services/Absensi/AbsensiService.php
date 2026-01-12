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
        
        // Get holiday name if is_hari_libur
        if ($absensi->is_hari_libur) {
            $holidayType = $this->getHolidayType($absensi->tanggal);
            if ($holidayType === 'NASIONAL') {
                $nasional = RefLiburNasional::whereDate('tanggal', $absensi->tanggal)->first();
                $abs['holiday_name'] = $nasional?->keterangan ?? 'Libur Nasional';
            } elseif ($holidayType === 'MINGGU') {
                $abs['holiday_name'] = 'Hari Minggu';
            } elseif ($holidayType === 'PT') {
                $pt = RefLiburPt::whereDate('tanggal', $absensi->tanggal)->first();
                $abs['holiday_name'] = $pt?->keterangan ?? 'Libur PT';
            } else {
                $abs['holiday_name'] = 'Hari Libur';
            }
        } else {
            $abs['holiday_name'] = null;
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
        // Check holiday type for the date
        $holidayType = $this->getHolidayType($data['tanggal']);
        $isHariLibur = $holidayType !== null;
        
        // Get appropriate tarif lembur based on holiday type
        $tarifLembur = $this->getTarifLembur($holidayType);
        $idTarifLembur = $tarifLembur?->id_tarif ?? null;
        $tarifPerJam = $tarifLembur?->tarif_per_jam ?? 0;
        
        // Calculate total lembur
        // For holiday attendance: all work hours count as overtime
        $totalJamKerja = (float) ($data['total_jam_kerja'] ?? 0);
        $totalLemburInput = (float) ($data['total_lembur'] ?? 0);
        
        if ($isHariLibur) {
            // On holidays, work hours are added to overtime
            $totalLembur = $totalJamKerja + $totalLemburInput;
        } else {
            $totalLembur = $totalLemburInput;
        }
        
        // Calculate nominal lembur
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
        // Check holiday type for the date
        $holidayType = $this->getHolidayType($data['tanggal']);
        $isHariLibur = $holidayType !== null;
        
        // Get appropriate tarif lembur based on holiday type
        $tarifLembur = $this->getTarifLembur($holidayType);
        $idTarifLembur = $tarifLembur?->id_tarif ?? null;
        $tarifPerJam = $tarifLembur?->tarif_per_jam ?? 0;
        
        // Calculate total lembur
        // For holiday attendance: all work hours count as overtime
        $totalJamKerja = (float) ($data['total_jam_kerja'] ?? 0);
        $totalLemburInput = (float) ($data['total_lembur'] ?? 0);
        
        if ($isHariLibur) {
            // On holidays, work hours are added to overtime
            $totalLembur = $totalJamKerja + $totalLemburInput;
        } else {
            $totalLembur = $totalLemburInput;
        }
        
        // Calculate nominal lembur
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
     * Get appropriate tarif lembur based on holiday type
     * Returns "Lembur di Hari Nasional" for national holidays,
     * "Lembur hari Libur" for Sunday/PT holidays,
     * "Lembur Biasa" for regular days
     * 
     * @param string|null $holidayType - NASIONAL, MINGGU, PT, or null for regular day
     */
    public function getTarifLembur(?string $holidayType): ?TarifLembur
    {
        if ($holidayType === 'NASIONAL') {
            // Try to find "Lembur di Hari Nasional" tarif (highest priority)
            $tarif = TarifLembur::where('nama_tarif', 'LIKE', '%Nasional%')->first();
            if ($tarif) return $tarif;
        }
        
        if ($holidayType === 'MINGGU' || $holidayType === 'PT') {
            // Try to find "Lembur hari Libur" tarif
            $tarif = TarifLembur::where('nama_tarif', 'LIKE', '%Libur%')
                ->where('nama_tarif', 'NOT LIKE', '%Nasional%')
                ->first();
            if ($tarif) return $tarif;
        }
        
        // Default to "Lembur Biasa" for regular days
        $tarif = TarifLembur::where('nama_tarif', 'LIKE', '%Biasa%')->first();
        if ($tarif) return $tarif;
        
        // Fallback to first tarif
        return TarifLembur::first();
    }

    /**
     * Get holiday type for a specific date
     * Returns: 'NASIONAL', 'MINGGU', 'PT', or null for regular day
     */
    public function getHolidayType(string $date): ?string
    {
        // Check Libur Nasional first (highest priority)
        if (RefLiburNasional::whereDate('tanggal', $date)->exists()) {
            return 'NASIONAL';
        }
        
        // Check Sunday
        if ((int) date('w', strtotime($date)) === 0) {
            return 'MINGGU';
        }
        
        // Check Libur PT
        if (RefLiburPt::whereDate('tanggal', $date)->exists()) {
            return 'PT';
        }
        
        return null;
    }

    /**
     * Get holiday info for a specific date (for AJAX)
     */
    public function getHolidayInfo(string $date): array
    {
        $holidayType = $this->getHolidayType($date);
        $isHoliday = $holidayType !== null;
        $tarif = $this->getTarifLembur($holidayType);
        
        $holidayName = null;
        if ($holidayType === 'NASIONAL') {
            $nasional = RefLiburNasional::whereDate('tanggal', $date)->first();
            $holidayName = $nasional?->keterangan ?? 'Libur Nasional';
        } elseif ($holidayType === 'MINGGU') {
            $holidayName = 'Hari Minggu';
        } elseif ($holidayType === 'PT') {
            $pt = RefLiburPt::whereDate('tanggal', $date)->first();
            $holidayName = $pt?->keterangan ?? 'Libur PT';
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
            return DB::connection('mysql')
                ->table('sdm_jadwal_karyawan as sjk')
                ->join('master_jadwal_kerja as mjk', 'mjk.id_jadwal', '=', 'sjk.id_jadwal')
                ->select([
                    'sjk.id_jadwal_karyawan',
                    'mjk.nama_jadwal as nama',
                    'mjk.jam_masuk',
                    'mjk.jam_pulang',
                    'sjk.tanggal_mulai',
                    'sjk.tanggal_selesai',
                ])
                ->orderBy('mjk.nama_jadwal')
                ->get()
                ->map(fn ($r) => (array) $r);
        } catch (Throwable) {
            return collect([]);
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

    private function getJadwalName(int $idJadwalKaryawan): ?string
    {
        try {
            $row = DB::connection('mysql')
                ->table('sdm_jadwal_karyawan as sjk')
                ->join('master_jadwal_kerja as mjk', 'mjk.id_jadwal', '=', 'sjk.id_jadwal')
                ->where('sjk.id_jadwal_karyawan', $idJadwalKaryawan)
                ->select(['mjk.nama_jadwal', 'mjk.jam_masuk', 'mjk.jam_pulang'])
                ->first();

            if (!$row) return null;
            return $row->nama_jadwal . ' (' . $row->jam_masuk . ' - ' . $row->jam_pulang . ')';
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Resolve id_jadwal_karyawan for a given SDM on a specific date
     */
    public function resolveJadwalForSdm(int $idSdm, string $tanggal): ?int
    {
        $row = DB::connection('mysql')
            ->table('sdm_jadwal_karyawan')
            ->where('id_sdm', $idSdm)
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->whereDate('tanggal_selesai', '>=', $tanggal)
            ->orderByDesc('tanggal_mulai')
            ->first(['id_jadwal_karyawan']);

        return $row?->id_jadwal_karyawan;
    }
    
}

