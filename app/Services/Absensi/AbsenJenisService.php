<?php

namespace App\Services\Absensi;

use App\Models\Absensi\AbsenJenis;
use Illuminate\Database\Eloquent\Builder;

final class AbsenJenisService
{
    public function getListQuery(): Builder
    {
        return AbsenJenis::query()->select([
            'id_jenis_absen',
            'nama_absen',
            'kategori',
            'potong_gaji',
        ]);
    }

    public function find(int $id): ?AbsenJenis
    {
        return AbsenJenis::find($id);
    }

    public function create(array $data): AbsenJenis
    {
        return AbsenJenis::create($data);
    }

    public function update(AbsenJenis $row, array $data): AbsenJenis
    {
        $row->update($data);
        return $row;
    }

    public function delete(AbsenJenis $row): void
    {
        $row->delete();
    }
}
