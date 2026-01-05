<?php

namespace App\Services\Gaji;

use App\Models\Gaji\TarifLembur;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TarifLemburService
{
    public function getListQuery(): Builder
    {
        return TarifLembur::query()->orderBy('nama_tarif');
    }

    public function find(int $id): ?TarifLembur
    {
        return TarifLembur::find($id);
    }

    public function create(array $data): TarifLembur
    {
        return TarifLembur::create($data);
    }

    public function update(TarifLembur $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(TarifLembur $model): bool
    {
        return $model->delete();
    }

    public function getOptions(): Collection
    {
        return TarifLembur::query()
            ->select(['id_tarif', 'nama_tarif', 'tarif_per_jam'])
            ->orderBy('nama_tarif')
            ->get();
    }
}
