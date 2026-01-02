<?php

namespace App\Services\Gaji;

use App\Models\Gaji\GajiJenisKomponen;
use Illuminate\Database\Eloquent\Builder;

final class GajiJenisKomponenService
{
    public function getListQuery(): Builder
    {
        return GajiJenisKomponen::query()->select([
            'id_jenis_komponen',
            'nama_komponen',
            'jenis',
        ]);
    }

    public function getDetail(string|int $id): ?GajiJenisKomponen
    {
        return GajiJenisKomponen::find($id);
    }

    public function create(array $data): GajiJenisKomponen
    {
        return GajiJenisKomponen::create($data);
    }

    public function update(GajiJenisKomponen $row, array $data): GajiJenisKomponen
    {
        $row->update($data);
        return $row;
    }

    public function delete(GajiJenisKomponen $row): void
    {
        $row->delete();
    }
}
