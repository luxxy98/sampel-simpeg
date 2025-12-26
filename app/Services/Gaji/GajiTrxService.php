<?php

namespace App\Services\Gaji;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class GajiTrxService
{
    public function getListQuery(?int $idPeriode = null, ?string $status = null): Builder
    {
        $q = DB::table('gaji_trx as gt')
            ->leftJoin('gaji_periode as gp', 'gp.id_periode', '=', 'gt.id_periode')
            ->leftJoin('person_sdm as ps', 'ps.id_sdm', '=', 'gt.id_sdm')
            ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
            ->select([
                'gt.id_gaji',
                'gt.id_periode',
                DB::raw("CONCAT(gp.tahun,'-',LPAD(gp.bulan,2,'0')) as periode"),
                'gt.id_sdm',
                DB::raw('p.nama as sdm'),
                'gt.total_penghasilan',
                'gt.total_potongan',
                'gt.total_take_home_pay',
                'gt.status',
            ]);

        if ($idPeriode) $q->where('gt.id_periode', $idPeriode);
        if ($status) $q->where('gt.status', $status);

        return $q;
    }

    public function getDetailBundle(int $idGaji): ?array
    {
        $trx = DB::table('gaji_trx as gt')
            ->leftJoin('gaji_periode as gp', 'gp.id_periode', '=', 'gt.id_periode')
            ->leftJoin('person_sdm as ps', 'ps.id_sdm', '=', 'gt.id_sdm')
            ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
            ->select([
                'gt.id_gaji',
                'gt.id_periode',
                'gt.id_sdm',
                'gt.total_penghasilan',
                'gt.total_potongan',
                'gt.total_take_home_pay',
                'gt.status',
                DB::raw("CONCAT(gp.tahun,'-',LPAD(gp.bulan,2,'0')) as periode_label"),
                DB::raw("p.nama as sdm_nama"),
            ])
            ->where('gt.id_gaji', $idGaji)
            ->first();

        if (!$trx) return null;

        $detail = DB::table('gaji_detail as gd')
            ->leftJoin('gaji_komponen as gk', 'gk.id_gaji_komponen', '=', 'gd.id_gaji_komponen')
            ->leftJoin('gaji_jenis_komponen as gjk', 'gjk.id_jenis_komponen', '=', 'gk.id_jenis_komponen')
            ->select([
                'gd.id_gaji_detail',
                'gd.id_gaji',
                'gd.id_gaji_komponen',
                'gd.nominal',
                'gd.keterangan',
                DB::raw('gjk.nama_komponen as nama_komponen'),
            ])
            ->where('gd.id_gaji', $idGaji)
            ->orderBy('gd.id_gaji_detail')
            ->get()
            ->map(fn($r) => (array)$r)
            ->all();

        return [
            'trx' => (array)$trx,
            'detail' => $detail,
        ];
    }

    public function periodeOptions(): Collection
    {
        return DB::table('gaji_periode')
            ->select(['id_periode', 'tahun', 'bulan'])
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();
    }
}
