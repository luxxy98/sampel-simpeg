<?php

namespace App\Services\Sppd;

use App\Models\Sppd\Sppd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

use App\Services\Absensi\AbsensiService;

final class SppdService
{
    public function __construct(
        private readonly AbsensiService $absensiService
    ) {}

    // ... existing methods ...

    public function sdmOptions(): Collection
    {
        return \DB::table('person_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'person_sdm.id_sdm',
                'person.nama',
            ])
            ->orderBy('person.nama')
            ->get();
    }

    public function listQuery(?string $status, ?int $idSdm): Builder
    {
        $q = Sppd::query()
            ->leftJoin('person_sdm', 'person_sdm.id_sdm', '=', 'sppd.id_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'sppd.*',
                'person.nama',
            ])
            ->orderByDesc('sppd.id_sppd');

        if ($status) $q->where('sppd.status', $status);
        if ($idSdm) $q->where('sppd.id_sdm', $idSdm);

        return $q;
    }

    public function create(array $data, string $adminId): Sppd
    {
        $data['created_by'] = $adminId;
        $data['tanggal_surat'] = $data['tanggal_surat'] ?? Carbon::now()->format('Y-m-d');
        $data['status'] = 'draft';

        $data = $this->normalizeBiaya($data);

        return Sppd::create($data);
    }

    public function getDetailData(string $id): ?object
    {
        return \DB::table('sppd')
            ->leftJoin('person_sdm', 'person_sdm.id_sdm', '=', 'sppd.id_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'sppd.*',
                'person.nama',
            ])
            ->where('sppd.id_sppd', $id)
            ->first();
    }

    public function findById(string $id): ?Sppd
    {
        return Sppd::find($id);
    }

    public function update(Sppd $sppd, array $data): Sppd
    {
        // jika sudah disetujui/selesai, jangan diubah lewat edit biasa
        if (in_array($sppd->status, ['disetujui', 'selesai'], true)) {
            unset($data['status']);
        }

        $data = $this->normalizeBiaya($data);
        $sppd->update($data);

        return $sppd;
    }

    public function submit(Sppd $sppd): Sppd
    {
        $sppd->update(['status' => 'diajukan']);
        return $sppd;
    }

    public function approve(Sppd $sppd, string $status, ?string $catatan, string $adminId): Sppd
    {
        $sppd->update([
            'status' => $status, // disetujui / ditolak
            'catatan' => $catatan,
            'approved_by' => $adminId,
            'tanggal_persetujuan' => Carbon::now()->format('Y-m-d'),
        ]);

        // Jika disetujui, buatkan absensi DINAS LUAR (ID 8) otomatis
        if ($status === 'disetujui') {
            $startDate = Carbon::parse($sppd->tanggal_berangkat);
            $endDate = Carbon::parse($sppd->tanggal_pulang);

            while ($startDate->lte($endDate)) {
                $currentDate = $startDate->format('Y-m-d');

                // Cek apakah sudah ada absensi di tanggal ini untuk SDM ini
                $exists = \DB::table('absensi')
                    ->where('id_sdm', $sppd->id_sdm)
                    ->whereDate('tanggal', $currentDate)
                    ->exists();

                if (!$exists) {
                    // Coba resolve jadwal karyawan
                    $idJadwalKaryawan = $this->absensiService->resolveJadwalForSdm($sppd->id_sdm, $currentDate);

                    if ($idJadwalKaryawan) {
                        // Siapkan data absensi
                        $dataAbsensi = [
                            'tanggal' => $currentDate,
                            'id_jadwal_karyawan' => $idJadwalKaryawan,
                            'id_sdm' => $sppd->id_sdm,
                            'total_jam_kerja' => 8, // Asumsi standar 8 jam
                            'total_terlambat' => 0,
                            'total_pulang_awal' => 0,
                            'total_lembur' => 0,
                        ];

                        // Siapkan detail absensi (DINAS LUAR)
                        // Ambil jam masuk/pulang dari jadwal jika ada, atau default 08:00-16:00
                        $jadwal = \DB::table('master_jadwal_kerja')
                            ->join('sdm_jadwal_karyawan', 'sdm_jadwal_karyawan.id_jadwal', '=', 'master_jadwal_kerja.id_jadwal')
                            ->where('sdm_jadwal_karyawan.id_jadwal_karyawan', $idJadwalKaryawan)
                            ->first(['jam_masuk', 'jam_pulang']);

                        $jamMasuk = $jadwal->jam_masuk ?? '08:00';
                        $jamPulang = $jadwal->jam_pulang ?? '16:00';

                        $detailRows = [[
                            'id_jenis_absen' => 8, // ID 8 = DINAS LUAR (pastikan ID ini benar di database)
                            'waktu_mulai' => $jamMasuk,
                            'waktu_selesai' => $jamPulang,
                            'durasi_jam' => 8,
                            'lokasi_pulang' => $sppd->tujuan, // Isi lokasi pulang dengan tujuan dinas
                        ]];

                        // Create via AbsensiService
                        try {
                            $this->absensiService->create($dataAbsensi, $detailRows);
                        } catch (\Throwable $e) {
                            // Log error tapi jangan gagalkan proses approval SPPD
                            \Log::error("Gagal buat absensi otomatis untuk SPPD {$sppd->nomor_surat} tanggal {$currentDate}: " . $e->getMessage());
                        }
                    }
                }

                $startDate->addDay();
            }
        }

        return $sppd;
    }

    public function selesai(Sppd $sppd): Sppd
    {
        $sppd->update(['status' => 'selesai']);
        return $sppd;
    }

    public function delete(Sppd $sppd): void
    {
        $sppd->delete();
    }

    private function normalizeBiaya(array $data): array
    {
        $bt = (int) ($data['biaya_transport'] ?? 0);
        $bp = (int) ($data['biaya_penginapan'] ?? 0);
        $uh = (int) ($data['uang_harian'] ?? 0);
        $bl = (int) ($data['biaya_lainnya'] ?? 0);

        $data['biaya_transport'] = max(0, $bt);
        $data['biaya_penginapan'] = max(0, $bp);
        $data['uang_harian'] = max(0, $uh);
        $data['biaya_lainnya'] = max(0, $bl);
        $data['total_biaya'] = $data['biaya_transport'] + $data['biaya_penginapan'] + $data['uang_harian'] + $data['biaya_lainnya'];

        return $data;
    }
}
