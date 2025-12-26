<?php

namespace App\Services\Absensi;

use App\Models\Absensi\Absensi;
use App\Models\Absensi\AbsensiDetail;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

final class AbsensiService
{
    public function getListData(?string $tanggalMulai = null, ?string $tanggalSelesai = null, ?int $idSdm = null): Builder
    {
        $mainDb = config('database.connections.mysql.database');

        $q = DB::connection('absensi')->table('absensi as a')
            ->leftJoin("{$mainDb}.person_sdm as ps", 'ps.id_sdm', '=', 'a.id_sdm')
            ->leftJoin("{$mainDb}.person as p", 'p.id_person', '=', 'ps.id_person')
            ->select([
                'a.id_absensi',
                'a.tanggal',
                'a.id_sdm',
                DB::raw('p.nama as sdm'),
                'a.id_jadwal_karyawan',
                DB::raw("CONCAT('#',a.id_jadwal_karyawan) as jadwal"),
                'a.total_jam_kerja',
                'a.total_terlambat',
                'a.total_pulang_awal',
            ]);

        if ($tanggalMulai) $q->whereDate('a.tanggal', '>=', $tanggalMulai);
        if ($tanggalSelesai) $q->whereDate('a.tanggal', '<=', $tanggalSelesai);
        if ($idSdm) $q->where('a.id_sdm', $idSdm);

        return $q;
    }

    public function findById(string $id): ?Absensi
    {
        return Absensi::find($id);
    }

    public function getDetailBundle(string $id): ?array
    {
        $absensiRow = $this->getListData()->where('a.id_absensi', (int)$id)->first();
        if (!$absensiRow) return null;

        $absensi = (array)$absensiRow;
        $absensi['sdm_nama'] = $absensi['sdm'] ?? null;
        $absensi['jadwal_nama'] = $absensi['jadwal'] ?? null;

        $detail = DB::connection('absensi')->table('absensi_detail as ad')
            ->leftJoin('absen_jenis as aj', 'aj.id_jenis_absen', '=', 'ad.id_jenis_absen')
            ->select([
                'ad.id_detail',
                'ad.id_absensi',
                'ad.id_jenis_absen',
                DB::raw('aj.nama_absen as nama_absen'),
                'ad.waktu_mulai',
                'ad.waktu_selesai',
                'ad.durasi_jam',
                'ad.lokasi_pulang',
            ])
            ->where('ad.id_absensi', (int)$id)
            ->orderBy('ad.id_detail')
            ->get()
            ->map(fn($r) => (array)$r)
            ->all();

        return ['absensi' => $absensi, 'detail' => $detail];
    }

    public function create(array $data, array $detailRows): Absensi
    {
        $absensi = Absensi::create($data);

        foreach ($detailRows as $row) {
            $row['id_absensi'] = $absensi->id_absensi;
            AbsensiDetail::create($row);
        }

        return $absensi;
    }

    public function update(Absensi $absensi, array $data, array $detailRows): Absensi
    {
        $absensi->update($data);

        AbsensiDetail::where('id_absensi', $absensi->id_absensi)->delete();
        foreach ($detailRows as $row) {
            $row['id_absensi'] = $absensi->id_absensi;
            AbsensiDetail::create($row);
        }

        return $absensi;
    }

    public function delete(Absensi $absensi): void
    {
        AbsensiDetail::where('id_absensi', $absensi->id_absensi)->delete();
        $absensi->delete();
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
        return DB::connection('absensi')->table('absen_jenis')
            ->select(['id_jenis_absen', 'nama_absen'])
            ->orderBy('kategori')
            ->orderBy('nama_absen')
            ->get();
    }

    public function jadwalOptions(): Collection
    {
        return collect([
            ['id_jadwal_karyawan' => 1, 'nama' => 'Default (#1)'],
        ]);
    }
}
