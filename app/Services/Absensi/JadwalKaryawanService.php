<?php

namespace App\Services\Absensi;

use App\Models\Absensi\JadwalKaryawan;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

final class JadwalKaryawanService
{
    public function getListQuery(): Builder
    {
        return DB::connection('mysql')
        ->table('sdm_jadwal_karyawan as sjk')
        ->leftJoin('person_sdm as ps', 'ps.id_sdm', '=', 'sjk.id_sdm')
        ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
        ->leftJoin('master_jadwal_kerja as mjk', 'mjk.id_jadwal', '=', 'sjk.id_jadwal')
        ->select([
            'sjk.id_jadwal_karyawan',
            'sjk.id_sdm',
            DB::raw('COALESCE(p.nama, CONCAT("SDM#", sjk.id_sdm)) as nama_sdm'),
            'sjk.id_jadwal',
            DB::raw('COALESCE(mjk.nama_jadwal, CONCAT("JADWAL#", sjk.id_jadwal)) as nama_jadwal'),
            'mjk.jam_masuk',
            'mjk.jam_pulang',
            'sjk.tanggal_mulai',
            'sjk.tanggal_selesai',
            'sjk.dibuat_oleh',
        ])
        ->orderBy('p.nama')
        ->orderBy('sjk.tanggal_mulai');
    }

    public function find(int $id): ?JadwalKaryawan
    {
        return JadwalKaryawan::query()->whereKey($id)->first();
    }

    public function create(array $data): JadwalKaryawan
    {
        return JadwalKaryawan::create($data);
    }

    public function update(JadwalKaryawan $row, array $data): void
    {
        $row->update($data);
    }

    public function delete(JadwalKaryawan $row): void
    {
        $row->delete();
    }
}
