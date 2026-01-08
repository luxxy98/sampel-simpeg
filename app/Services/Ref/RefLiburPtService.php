<?php

namespace App\Services\Ref;

use App\Models\Ref\RefLiburPt;
use Illuminate\Support\Collection;

final class RefLiburPtService
{
    public function getListData(): Collection
    {
        return RefLiburPt::query()->orderByDesc('tanggal')->get();
    }

    public function getDetailData(string $id): ?RefLiburPt
    {
        return $this->findById($id);
    }

    public function findById(string $id): ?RefLiburPt
    {
        return RefLiburPt::query()->find($id);
    }

    public function create(array $data): RefLiburPt
    {
        return RefLiburPt::query()->create($data);
    }

    public function update(RefLiburPt $liburPt, array $data): RefLiburPt
    {
        $liburPt->update($data);

        return $liburPt;
    }

    public function delete(RefLiburPt $liburPt): void
    {
        $liburPt->delete();
    }
}
