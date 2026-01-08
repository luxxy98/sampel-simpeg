<?php

namespace App\Services\Ref;

use App\Models\Ref\RefLiburNasional;
use Illuminate\Support\Collection;

final class RefLiburNasionalService
{
    public function getListData(): Collection
    {
        return RefLiburNasional::query()->orderByDesc('tanggal')->get();
    }

    public function getDetailData(string $id): ?RefLiburNasional
    {
        return $this->findById($id);
    }

    public function findById(string $id): ?RefLiburNasional
    {
        return RefLiburNasional::query()->find($id);
    }

    public function create(array $data): RefLiburNasional
    {
        return RefLiburNasional::query()->create($data);
    }

    public function update(RefLiburNasional $liburNasional, array $data): RefLiburNasional
    {
        $liburNasional->update($data);

        return $liburNasional;
    }

    public function delete(RefLiburNasional $liburNasional): void
    {
        $liburNasional->delete();
    }
}
