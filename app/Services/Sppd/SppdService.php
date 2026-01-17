<?php

namespace App\Services\Sppd;

use App\Models\Sppd\Sppd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class SppdService
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

    public function listQuery(?string $status, ?int $idSdm): Builder
    {
        $q = Sppd::query()
            ->leftJoin('person_sdm', 'person_sdm.id_sdm', '=', 'sppd.id_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'sppd.*',
                'person.nama',
            ])
            ->orderByDesc('sppd.id_sppd');

        if ($status) $q->where('sppd.status', $status);
        if ($idSdm) $q->where('sppd.id_sdm', $idSdm);

        return $q;
    }

    public function create(array $data, string $adminId): Sppd
    {
        $data['created_by'] = $adminId;
        $data['tanggal_surat'] = $data['tanggal_surat'] ?? Carbon::now()->format('Y-m-d');
        $data['status'] = 'draft';

        $data = $this->normalizeBiaya($data);

        return Sppd::create($data);
    }

    public function getDetailData(string $id): ?object
    {
        return \DB::table('sppd')
            ->leftJoin('person_sdm', 'person_sdm.id_sdm', '=', 'sppd.id_sdm')
            ->leftJoin('person', 'person.id_person', '=', 'person_sdm.id_person')
            ->select([
                'sppd.*',
                'person.nama',
            ])
            ->where('sppd.id_sppd', $id)
            ->first();
    }

    public function findById(string $id): ?Sppd
    {
        return Sppd::find($id);
    }

    public function update(Sppd $sppd, array $data): Sppd
    {
        // jika sudah disetujui/selesai, jangan diubah lewat edit biasa
        if (in_array($sppd->status, ['disetujui', 'selesai'], true)) {
            unset($data['status']);
        }

        $data = $this->normalizeBiaya($data);
        $sppd->update($data);

        return $sppd;
    }

    public function submit(Sppd $sppd): Sppd
    {
        $sppd->update(['status' => 'diajukan']);
        return $sppd;
    }

    public function approve(Sppd $sppd, string $status, ?string $catatan, string $adminId): Sppd
    {
        $sppd->update([
            'status' => $status, // disetujui / ditolak
            'catatan' => $catatan,
            'approved_by' => $adminId,
            'tanggal_persetujuan' => Carbon::now()->format('Y-m-d'),
        ]);

        return $sppd;
    }

    public function selesai(Sppd $sppd): Sppd
    {
        $sppd->update(['status' => 'selesai']);
        return $sppd;
    }

    public function delete(Sppd $sppd): void
    {
        $sppd->delete();
    }

    private function normalizeBiaya(array $data): array
    {
        $bt = (int) ($data['biaya_transport'] ?? 0);
        $bp = (int) ($data['biaya_penginapan'] ?? 0);
        $uh = (int) ($data['uang_harian'] ?? 0);
        $bl = (int) ($data['biaya_lainnya'] ?? 0);

        $data['biaya_transport'] = max(0, $bt);
        $data['biaya_penginapan'] = max(0, $bp);
        $data['uang_harian'] = max(0, $uh);
        $data['biaya_lainnya'] = max(0, $bl);
        $data['total_biaya'] = $data['biaya_transport'] + $data['biaya_penginapan'] + $data['uang_harian'] + $data['biaya_lainnya'];

        return $data;
    }
}
