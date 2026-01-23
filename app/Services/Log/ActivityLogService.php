<?php

namespace App\Services\Log;

use App\Models\App\Audit;
use Illuminate\Support\Collection;

final readonly class ActivityLogService
{
    public function getListData(): Collection
    {
        return Audit::query()
            ->select([
                'id',
                'created_at',
                'event',
                'auditable_type',
                'auditable_id',
                'user_type',
                'user_id',
                'ip_address',
                'url',
            ])
            ->orderByDesc('created_at')
            ->limit(2000)
            ->get();
    }

    public function findById(int $id): ?Audit
    {
        return Audit::query()->where('id', $id)->first();
    }
}
