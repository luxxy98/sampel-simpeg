<?php

namespace App\Services\Absensi;

use App\Models\Absensi\Absensi;
use App\Models\Absensi\JadwalKaryawan;
use App\Models\Sdm\PersonSdm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class AbsensiService
{
    public function getListData(Request $request): Builder
    {
        $masterDb = config('database.connections.mysql.database');

        return Absensi::query()
            ->leftJoin($masterDb . '.person_sdm as person_sdm', 'person_sdm.id_sdm', '=', 'absensi.id_sdm')
            ->leftJoin($masterDb . '.person as person', 'person.id_person', '=', 'person_sdm.id_person')
            ->leftJoin('sdm_jadwal_karyawan', 'sdm_jadwal_karyawan.id_jadwal_karyawan', '=', 'absensi.id_jadwal_karyawan')
            ->select([
                'absensi.id_absensi',
                'absensi.tanggal',
                'person.nama as nama_sdm',
                'sdm_jadwal_karyawan.nama_jadwal as jadwal',
                'absensi.total_jam_kerja',
                'absensi.total_terlambat',
                'absensi.total_pulang_awal',
            ])
            ->orderByDesc('absensi.tanggal');
    }


    public function create(array $data): Absensi
    {
        return Absensi::create($data);
    }

    public function findById(string $id): ?Absensi
    {
        return Absensi::find($id);
    }

    public function update(Absensi $absensi, array $data): Absensi
    {
        $absensi->update($data);
        return $absensi;
    }

    public function getEditData(string $id): ?array
    {
        $masterDb = config('database.connections.mysql.database'); // <-- TARUH DI SINI

        $row = Absensi::query()
            ->leftJoin($masterDb . '.person_sdm as person_sdm', 'person_sdm.id_sdm', '=', 'absensi.id_sdm')
            ->leftJoin($masterDb . '.person as person', 'person.id_person', '=', 'person_sdm.id_person')
            ->leftJoin('sdm_jadwal_karyawan', 'sdm_jadwal_karyawan.id_jadwal_karyawan', '=', 'absensi.id_jadwal_karyawan')
            ->where('absensi.id_absensi', $id)
            ->select([
                'absensi.id_absensi',
                'absensi.tanggal',
                'absensi.id_sdm',
                'absensi.id_jadwal_karyawan',
                'person.nama as nama_sdm',
                'sdm_jadwal_karyawan.nama_jadwal as jadwal',
                'absensi.total_jam_kerja',
                'absensi.total_terlambat',
                'absensi.total_pulang_awal',
            ])
            ->first();

        return $row ? $row->toArray() : null;
    }


    // ✅ ini yang dipakai openDetailAbsensi(id)
    public function getDetailPayload(string $id): ?array
    {
        $absensi = $this->getEditData($id);
        if (!$absensi) return null;

        $detail = DB::connection('absensigaji')
            ->table('absensi_detail')
            ->join('absen_jenis', 'absen_jenis.id_jenis_absen', '=', 'absensi_detail.id_jenis_absen')
            ->where('absensi_detail.id_absensi', $id)


            ->orderBy('absensi_detail.waktu_mulai')
            ->get([
                'absen_jenis.nama_absen',
                'absen_jenis.kategori',
                'absensi_detail.waktu_mulai',
                'absensi_detail.waktu_selesai',
                'absensi_detail.durasi_jam',
                'absensi_detail.lokasi_pulang',
            ])
            ->map(fn($d) => [
                'nama_absen' => $d->nama_absen,
                'kategori' => $d->kategori,
                'waktu_mulai' => $d->waktu_mulai,
                'waktu_selesai' => $d->waktu_selesai,
                'durasi_jam' => (float) $d->durasi_jam,
                'lokasi_pulang' => $d->lokasi_pulang,
            ])
            ->values()
            ->all();

        return [
            'absensi' => [
                'tanggal' => $absensi['tanggal'],
                'nama_sdm' => $absensi['nama_sdm'],
                'jadwal' => $absensi['jadwal'],
                'total_jam_kerja' => (float) $absensi['total_jam_kerja'],
                'total_terlambat' => (float) $absensi['total_terlambat'],
                'total_pulang_awal' => (float) $absensi['total_pulang_awal'],
            ],
            'detail' => $detail,
        ];
    }

    public function getSdmDropdown(): Collection
    {
        return PersonSdm::query()
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select(['person_sdm.id_sdm', 'person.nama'])
            ->orderBy('person.nama')
            ->get();
    }

    public function getJadwalDropdown(): Collection
    {
        return JadwalKaryawan::query()
            ->select(['id_jadwal_karyawan', 'nama_jadwal', 'keterangan'])
            ->orderBy('nama_jadwal')
            ->get();
    }

    public function deleteById(string $id): bool
{
    $row = Absensi::query()->where('id_absensi', $id)->first();
    if (!$row) return false;

    // hapus detail dulu (biar aman FK)
    DB::connection($row->getConnectionName() ?? 'absensigaji')
        ->table('absensi_detail')
        ->where('id_absensi', $id)
        ->delete();

    $row->delete();
    return true;
}

}
