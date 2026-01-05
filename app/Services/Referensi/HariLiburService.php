<?php

namespace App\Services\Referensi;

use App\Models\Referensi\HariLibur;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HariLiburService
{
    public function getListQuery(): Builder
    {
        return HariLibur::query()->orderByDesc('tanggal');
    }

    public function find(int $id): ?HariLibur
    {
        return HariLibur::find($id);
    }

    public function create(array $data): HariLibur
    {
        return HariLibur::create($data);
    }

    public function update(HariLibur $model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete(HariLibur $model): bool
    {
        return $model->delete();
    }

    /**
     * Check if a date is a holiday (in hari_libur table or is Sunday)
     */
    public function isHoliday(string $date): bool
    {
        // Check if Sunday (day of week = 0)
        $dayOfWeek = date('w', strtotime($date));
        if ($dayOfWeek == 0) {
            return true;
        }

        // Check in hari_libur table
        return HariLibur::where('tanggal', $date)->exists();
    }

    /**
     * Get all holidays for a specific month/year
     */
    public function getHolidaysForMonth(int $year, int $month): Collection
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        return HariLibur::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get();
    }
}
