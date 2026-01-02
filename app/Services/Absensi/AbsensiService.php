<?php

namespace App\Services\Absensi;

use App\Models\Absensi\Absensi;
use App\Models\Absensi\AbsensiDetail;
use App\Models\Absensi\AbsenJenis;
use App\Models\Absensi\JadwalKaryawan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AbsensiService
{
    public function listQuery(?string $tanggalMulai = null, ?string $tanggalSelesai = null, ?int $idSdm = null): Builder
    {
        // Gunakan Eloquent agar otomatis pakai koneksi yg ada di Model
        $q = Absensi::query()
            ->select([
                'id_absensi', // tanpa alias 'a.' karena Eloquent
                'tanggal',
                'id_sdm',
                'id_jadwal_karyawan',
                'total_jam_kerja',
                'total_terlambat',
                'total_pulang_awal',
            ]);

        // Debug: Sementara komentar filter dulu untuk memastikan data muncul
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

    public function create(array $data, array $detailRows): int
    {
        $absensi = Absensi::create([
            'tanggal' => $data['tanggal'],
            'id_jadwal_karyawan' => $data['id_jadwal_karyawan'],
            'id_sdm' => $data['id_sdm'],
            'total_jam_kerja' => $data['total_jam_kerja'] ?? 0,
            'total_terlambat' => $data['total_terlambat'] ?? 0,
            'total_pulang_awal' => $data['total_pulang_awal'] ?? 0,
        ]);

        foreach ($detailRows as $row) {
            $row['id_absensi'] = (int) $absensi->getKey();
            AbsensiDetail::create($row);
        }

        return (int) $absensi->getKey();
    }

    public function update(Absensi $absensi, array $data, array $detailRows): void
    {
        $absensi->update([
            'tanggal' => $data['tanggal'],
            'id_jadwal_karyawan' => $data['id_jadwal_karyawan'],
            'id_sdm' => $data['id_sdm'],
            'total_jam_kerja' => $data['total_jam_kerja'] ?? 0,
            'total_terlambat' => $data['total_terlambat'] ?? 0,
            'total_pulang_awal' => $data['total_pulang_awal'] ?? 0,
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
                ->select(['id_jadwal_karyawan', DB::raw('nama_jadwal as nama')])
                ->orderBy('nama_jadwal')
                ->get()
                ->map(fn ($r) => $r->toArray());
        } catch (Throwable) {
            return collect([['id_jadwal_karyawan' => 1, 'nama' => 'Default (#1)']]);
        }
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
