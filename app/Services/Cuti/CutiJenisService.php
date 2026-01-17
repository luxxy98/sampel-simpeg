<?php

namespace App\Services\Cuti;

use App\Models\Cuti\CutiJenis;
use Illuminate\Support\Collection;

final class CutiJenisService
{
    public function getListData(): Collection
    {
        return CutiJenis::orderBy('nama_jenis')->get();
    }

    public function optionsActive(): Collection
    {
        return CutiJenis::query()
            ->select(['id_jenis_cuti', 'nama_jenis'])
            ->where('status', 'active')
            ->orderBy('nama_jenis')
            ->get();
    }

    public function create(array $data): CutiJenis
    {
        return CutiJenis::create($data);
    }

    public function getDetailData(string $id): ?CutiJenis
    {
        return CutiJenis::find($id);
    }

    public function findById(string $id): ?CutiJenis
    {
        return CutiJenis::find($id);
    }

    public function update(CutiJenis $jenis, array $data): CutiJenis
    {
        $jenis->update($data);
        return $jenis;
    }

    public function delete(CutiJenis $jenis): void
    {
        $jenis->delete();
    }
}
