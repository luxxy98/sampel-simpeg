<?php

namespace App\Services\Absensi;

use App\Models\Absensi\MasterJadwalKerja;
use Illuminate\Database\Eloquent\Builder;

final class MasterJadwalKerjaService
{
    public function getListQuery(): Builder
    {
        return MasterJadwalKerja::query()
            ->select([
                'id_jadwal',
                'nama_jadwal',
                'keterangan',
                'jam_masuk',
                'jam_pulang',
            ])
            ->orderBy('nama_jadwal');
    }

    public function find(int $id): ?MasterJadwalKerja
    {
        return MasterJadwalKerja::query()->whereKey($id)->first();
    }

    public function create(array $data): MasterJadwalKerja
    {
        return MasterJadwalKerja::create($data);
    }

    public function update(MasterJadwalKerja $row, array $data): void
    {
        $row->update($data);
    }

    public function delete(MasterJadwalKerja $row): void
    {
        $row->delete();
    }
}
