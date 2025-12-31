<?php

namespace App\Services\Gaji;

use App\Models\Gaji\GajiPeriode;
use Illuminate\Database\Eloquent\Builder;

final class GajiPeriodeService
{
    public function getListQuery(): Builder
    {
        return GajiPeriode::query()->select([
            'id_periode',
            'tahun',
            'bulan',
            'tanggal_mulai',
            'tanggal_selesai',
            'tanggal_penggajian',
            'status',
            'status_peninjauan',
        ]);
    }

    public function find(int|string $id): ?GajiPeriode
    {
        return GajiPeriode::find($id);
    }

    public function create(array $data): GajiPeriode
    {
        return GajiPeriode::create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        $row = $this->find($id);
        return $row ? $row->update($data) : false;
    }

    public function delete(int|string $id): bool
    {
        $row = $this->find($id);
        return $row ? (bool) $row->delete() : false;
    }
}
