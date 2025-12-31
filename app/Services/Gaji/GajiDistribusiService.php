<?php

namespace App\Services\Gaji;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class GajiDistribusiService
{
    private function mainDatabaseName(): string
    {
        $mainConn = (string) config('database.default', 'mysql');
        return (string) config("database.connections.{$mainConn}.database");
    }

    public function getListQuery(?int $idPeriode = null, ?string $statusTransfer = null): Builder
    {
        $mainDb = $this->mainDatabaseName();

        $q = DB::connection('absensigaji')->table('gaji_distribusi as gd')
            ->leftJoin('gaji_periode as gp', 'gp.id_periode', '=', 'gd.id_periode')
            ->leftJoin("{$mainDb}.person_sdm as ps", 'ps.id_sdm', '=', 'gd.id_sdm')
            ->leftJoin("{$mainDb}.person as p", 'p.id_person', '=', 'ps.id_person')
            ->leftJoin("{$mainDb}.sdm_rekening as sr", 'sr.id_rekening', '=', 'gd.id_rekening')
            ->select([
                'gd.id_distribusi',
                'gd.id_periode',
                DB::raw("CONCAT(gp.tahun,'-',LPAD(gp.bulan,2,'0')) as periode"),
                'gd.id_gaji',
                'gd.id_sdm',
                DB::raw('p.nama as sdm'),
                'gd.id_rekening',
                DB::raw("TRIM(CONCAT(IFNULL(sr.bank,''),' - ',IFNULL(sr.no_rekening,''))) as rekening"),
                'gd.jumlah_transfer',
                'gd.status_transfer',
                'gd.tanggal_transfer',
                'gd.catatan',
            ]);

        if ($idPeriode) $q->where('gd.id_periode', $idPeriode);
        if ($statusTransfer) $q->where('gd.status_transfer', $statusTransfer);

        return $q;
    }

    public function getDetailData(int $id): ?array
    {
        $mainDb = $this->mainDatabaseName();

        $row = DB::connection('absensigaji')->table('gaji_distribusi as gd')
            ->leftJoin('gaji_periode as gp', 'gp.id_periode', '=', 'gd.id_periode')
            ->leftJoin("{$mainDb}.person_sdm as ps", 'ps.id_sdm', '=', 'gd.id_sdm')
            ->leftJoin("{$mainDb}.person as p", 'p.id_person', '=', 'ps.id_person')
            ->leftJoin("{$mainDb}.sdm_rekening as sr", 'sr.id_rekening', '=', 'gd.id_rekening')
            ->select([
                'gd.*',
                DB::raw("CONCAT(gp.tahun,'-',LPAD(gp.bulan,2,'0')) as periode_label"),
                DB::raw('p.nama as sdm_nama'),
                DB::raw("TRIM(CONCAT(IFNULL(sr.bank,''),' - ',IFNULL(sr.no_rekening,''))) as rekening_label"),
            ])
            ->where('gd.id_distribusi', $id)
            ->first();

        return $row ? (array) $row : null;
    }

    public function periodeOptions(): Collection
    {
        return DB::connection('absensigaji')->table('gaji_periode')
            ->select(['id_periode', 'tahun', 'bulan'])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();
    }

    public function sdmOptions(): Collection
    {
        return DB::table('person_sdm as ps')
            ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
            ->select(['ps.id_sdm', DB::raw('p.nama as nama')])
            ->orderBy('p.nama')
            ->get();
    }

    public function rekeningOptions(): Collection
    {
        return DB::table('sdm_rekening')
            ->select([
                'id_rekening',
                'id_sdm',
                DB::raw("CONCAT(bank,' - ',no_rekening) as label"),
            ])
            ->orderBy('bank')
            ->orderBy('no_rekening')
            ->get();
    }

    public function trxOptions(): Collection
    {
        $mainDb = $this->mainDatabaseName();

        return DB::connection('absensigaji')->table('gaji_trx as gt')
            ->leftJoin('gaji_periode as gp', 'gp.id_periode', '=', 'gt.id_periode')
            ->leftJoin("{$mainDb}.person_sdm as ps", 'ps.id_sdm', '=', 'gt.id_sdm')
            ->leftJoin("{$mainDb}.person as p", 'p.id_person', '=', 'ps.id_person')
            ->select([
                'gt.id_gaji',
                DB::raw("CONCAT('TRX #',gt.id_gaji,' | ',p.nama,' | ',gp.tahun,'-',LPAD(gp.bulan,2,'0')) as label"),
            ])
            ->orderByDesc('gt.id_gaji')
            ->get();
    }
}
