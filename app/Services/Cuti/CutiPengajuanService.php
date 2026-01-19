<?php

namespace App\Services\Cuti;

use App\Models\Cuti\CutiPengajuan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class CutiPengajuanService
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

    public function listQuery(?string $tanggalMulai, ?string $tanggalSelesai, ?string $status, ?int $idSdm): Builder
    {
        $q = CutiPengajuan::query()
            ->leftJoin('cuti_jenis', 'cuti_jenis.id_jenis_cuti', '=', 'cuti_pengajuan.id_jenis_cuti')
            ->leftJoin('person_sdm', 'person_sdm.id_sdm', '=', 'cuti_pengajuan.id_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'cuti_pengajuan.id_cuti',
                'cuti_pengajuan.id_sdm',
                'cuti_pengajuan.id_jenis_cuti',
                'cuti_pengajuan.tanggal_mulai',
                'cuti_pengajuan.tanggal_selesai',
                'cuti_pengajuan.jumlah_hari',
                'cuti_pengajuan.alasan',
                'cuti_pengajuan.status',
                'cuti_pengajuan.tanggal_pengajuan',
                'cuti_pengajuan.tanggal_persetujuan',
                'cuti_pengajuan.approved_by',
                'cuti_pengajuan.catatan',
                'cuti_jenis.nama_jenis',
                'person.nama',
            ])
            ->orderByDesc('cuti_pengajuan.id_cuti');

        if ($tanggalMulai) $q->whereDate('cuti_pengajuan.tanggal_mulai', '>=', $tanggalMulai);
        if ($tanggalSelesai) $q->whereDate('cuti_pengajuan.tanggal_selesai', '<=', $tanggalSelesai);
        if ($status) $q->where('cuti_pengajuan.status', $status);
        if ($idSdm) $q->where('cuti_pengajuan.id_sdm', $idSdm);

        return $q;
    }

    public function create(array $data): CutiPengajuan
    {
        $mulai = Carbon::parse($data['tanggal_mulai']);
        $selesai = Carbon::parse($data['tanggal_selesai']);
        $jumlah = $mulai->diffInDays($selesai) + 1;

        $data['jumlah_hari'] = max(1, (int) $jumlah);
        $data['status'] = 'diajukan';
        $data['tanggal_pengajuan'] = Carbon::now()->format('Y-m-d');

        return CutiPengajuan::create($data);
    }

    public function getDetailData(string $id): ?object
    {
        return \DB::table('cuti_pengajuan')
            ->leftJoin('cuti_jenis', 'cuti_jenis.id_jenis_cuti', '=', 'cuti_pengajuan.id_jenis_cuti')
            ->leftJoin('person_sdm', 'person_sdm.id_sdm', '=', 'cuti_pengajuan.id_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->leftJoin('users', 'users.id', '=', 'cuti_pengajuan.approved_by')
            ->select([
                'cuti_pengajuan.*',
                'cuti_jenis.nama_jenis',
                'person.nama',
                'users.name as approver_name',
            ])
            ->where('cuti_pengajuan.id_cuti', $id)
            ->first();
    }

    public function findById(string $id): ?CutiPengajuan
    {
        return CutiPengajuan::find($id);
    }

    public function update(CutiPengajuan $cuti, array $data): CutiPengajuan
    {
        $mulai = Carbon::parse($data['tanggal_mulai']);
        $selesai = Carbon::parse($data['tanggal_selesai']);
        $jumlah = $mulai->diffInDays($selesai) + 1;

        $data['jumlah_hari'] = max(1, (int) $jumlah);

        // status tetap "diajukan" kalau belum diputuskan
        if (in_array($cuti->status, ['disetujui', 'ditolak'], true)) {
            // kalau sudah final, jangan ubah status via edit biasa
            unset($data['status']);
        }

        $cuti->update($data);
        return $cuti;
    }

    public function approve(CutiPengajuan $cuti, string $status, ?string $catatan, string $adminId): CutiPengajuan
    {
        $cuti->update([
            'status' => $status,
            'catatan' => $catatan,
            'approved_by' => $adminId,
            'tanggal_persetujuan' => Carbon::now()->format('Y-m-d'),
        ]);

        return $cuti;
    }

    public function delete(CutiPengajuan $cuti): void
    {
        $cuti->delete();
    }
}
