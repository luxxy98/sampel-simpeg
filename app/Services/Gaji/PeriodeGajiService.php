<?php

namespace App\Services\Gaji;

use App\Models\Gaji\PeriodeGaji;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class PeriodeGajiService
{
    public function getListData(Request $request): Builder
    {
        return PeriodeGaji::query()
            ->select([
                'id_periode','tahun','bulan','tanggal_mulai','tanggal_selesai',
                'status','status_peninjauan','tanggal_penggajian',
            ])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan');
    }

    public function create(array $data): PeriodeGaji
    {
        return PeriodeGaji::create($data);
    }

    public function findById(string $id): ?PeriodeGaji
    {
        return PeriodeGaji::find($id);
    }

    public function update(PeriodeGaji $periode, array $data): PeriodeGaji
    {
        $periode->update($data);
        return $periode;
    }

    public function getEditData(string $id): ?array
    {
        $row = PeriodeGaji::query()->where('id_periode', $id)->first();
        return $row ? $row->toArray() : null;
    }
}
