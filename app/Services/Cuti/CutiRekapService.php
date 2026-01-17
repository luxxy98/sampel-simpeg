<?php

namespace App\Services\Cuti;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

final class CutiRekapService
{
    public function sdmOptions(): Collection
    {
        return \DB::table('person_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'person_sdm.id_sdm',
                'person.nama',
            ])
            ->orderBy('person.nama')
            ->get();
    }

    public function rekapQuery(
        ?string $status,
        ?int $idSdm,
        ?int $idJenis
    ): Builder {
        $q = \DB::table('cuti_pengajuan')
            ->leftJoin('cuti_jenis', 'cuti_jenis.id_jenis_cuti', '=', 'cuti_pengajuan.id_jenis_cuti')
            ->leftJoin('person_sdm', 'person_sdm.id_sdm', '=', 'cuti_pengajuan.id_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'cuti_pengajuan.id_sdm',
                'person.nama',
                'cuti_pengajuan.id_jenis_cuti',
                'cuti_jenis.nama_jenis',
                \DB::raw('COUNT(cuti_pengajuan.id_cuti) AS jumlah_pengajuan'),
                \DB::raw('SUM(cuti_pengajuan.jumlah_hari) AS total_hari'),
                \DB::raw('MIN(cuti_pengajuan.tanggal_mulai) AS min_tanggal_mulai'),
                \DB::raw('MAX(cuti_pengajuan.tanggal_selesai) AS max_tanggal_selesai'),
            ])
            ->groupBy([
                'cuti_pengajuan.id_sdm',
                'person.nama',
                'cuti_pengajuan.id_jenis_cuti',
                'cuti_jenis.nama_jenis',
            ])
            ->orderBy('person.nama');

        if ($status) $q->where('cuti_pengajuan.status', $status);
        if ($idSdm) $q->where('cuti_pengajuan.id_sdm', $idSdm);
        if ($idJenis) $q->where('cuti_pengajuan.id_jenis_cuti', $idJenis);

        return $q;
    }
}
