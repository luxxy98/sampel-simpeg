<?php

namespace App\Services\Log;

use App\Models\Log\DbQueryLog;
use Illuminate\Support\Collection;

final readonly class DbQueryLogService
{
    public function getListData(): Collection
    {
        return DbQueryLog::query()
            ->select([
                'id',
                'created_at',
                'connection_name',
                'time_ms',
                'method',
                'route_name',
                'url',
                'user_name',
                'ip_address',
            ])
            ->orderByDesc('id')
            ->limit(2000)
            ->get();
    }

    public function findById(int $id): ?DbQueryLog
    {
        return DbQueryLog::query()->where('id', $id)->first();
    }
}
