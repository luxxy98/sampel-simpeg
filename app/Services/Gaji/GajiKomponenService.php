<?php

namespace App\Services\Gaji;

use App\Models\Gaji\GajiKomponen;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class GajiKomponenService
{
    private function mainDatabaseName(): string
    {
        $mainConn = (string) config('database.default', 'mysql');
        return (string) config("database.connections.{$mainConn}.database");
    }

    public function getListQuery(?int $idJabatan = null, ?string $jenis = null): Builder
    {
        $mainDb = $this->mainDatabaseName();

        $q = DB::connection('absensigaji')->table('gaji_komponen as gk')
            ->leftJoin("{$mainDb}.master_jabatan as mj", 'mj.id_jabatan', '=', 'gk.id_jabatan')
            ->leftJoin('gaji_jenis_komponen as gjk', 'gjk.id_jenis_komponen', '=', 'gk.id_jenis_komponen')
            ->select([
                'gk.id_gaji_komponen',
                'gk.id_jabatan',
                DB::raw('mj.jabatan as jabatan'),
                'gk.id_jenis_komponen',
                'gjk.nama_komponen',
                'gjk.jenis',
                'gk.nominal',
            ]);

        if ($idJabatan) $q->where('gk.id_jabatan', $idJabatan);
        if ($jenis) $q->where('gjk.jenis', $jenis);

        return $q;
    }

    public function findById(string|int $id): ?GajiKomponen
    {
        return GajiKomponen::find($id);
    }

    public function getDetailRow(string|int $id): ?object
    {
        $mainDb = $this->mainDatabaseName();

        return DB::connection('absensigaji')->table('gaji_komponen as gk')
            ->leftJoin("{$mainDb}.master_jabatan as mj", 'mj.id_jabatan', '=', 'gk.id_jabatan')
            ->leftJoin('gaji_jenis_komponen as gjk', 'gjk.id_jenis_komponen', '=', 'gk.id_jenis_komponen')
            ->select([
                'gk.*',
                DB::raw('mj.jabatan as jabatan'),
                'gjk.nama_komponen',
                'gjk.jenis',
            ])
            ->where('gk.id_gaji_komponen', (int) $id)
            ->first();
    }

    public function create(array $data): GajiKomponen
    {
        return GajiKomponen::create($data);
    }

    public function update(GajiKomponen $row, array $data): GajiKomponen
    {
        $row->update($data);
        return $row;
    }

    public function delete(GajiKomponen $row): void
    {
        $row->delete();
    }

    public function jabatanOptions(): Collection
    {
        // master_jabatan di DB utama (simpeg)
        return DB::table('master_jabatan')
            ->select([
                'id_jabatan',
                DB::raw('jabatan as nama_jabatan'),
            ])
            ->orderBy('jabatan')
            ->get();
    }

    public function jenisKomponenOptions(): Collection
    {
        // gaji_jenis_komponen di DB absensigaji
        return DB::connection('absensigaji')->table('gaji_jenis_komponen')
            ->select(['id_jenis_komponen', 'nama_komponen', 'jenis'])
            ->orderBy('jenis')
            ->orderBy('nama_komponen')
            ->get();
    }
}
