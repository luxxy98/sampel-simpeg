<?php

namespace App\Http\Controllers\Admin\Absensi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Absensi\AbsensiRequest;
use App\Services\Absensi\AbsensiService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AbsensiController extends Controller
{
    public function __construct(
        private readonly AbsensiService $service,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        $toPlainArray = static fn($r) => is_array($r) ? $r : (method_exists($r, 'toArray') ? $r->toArray() : (array) $r);


        $sdmOptions = $this->service->sdmOptions()->map($toPlainArray)->all();
        $jadwalOptions = $this->service->jadwalOptions()->map($toPlainArray)->all();
        $jenisAbsenOptions = $this->service->jenisAbsenOptions()->map($toPlainArray)->all();

        return view('admin.absensi.index', compact('sdmOptions', 'jadwalOptions', 'jenisAbsenOptions'));
    }

    public function list(Request $request): JsonResponse
    {
        // DEBUG: Log received filter parameters
        \Log::info('Absensi List Filter:', [
            'tanggal_mulai' => $request->get('tanggal_mulai'),
            'tanggal_selesai' => $request->get('tanggal_selesai'),
            'id_sdm' => $request->get('id_sdm'),
        ]);

        $sdmMap = $this->service->sdmOptions()
            ->mapWithKeys(function ($r) {
                $a = (array) $r;
                return [(int) ($a['id_sdm'] ?? 0) => (string) ($a['nama'] ?? '')];
            })
            ->filter()
            ->all();

        $jadwalMap = $this->service->jadwalOptions()
            ->mapWithKeys(function ($r) {
                $a = (array) $r;
                $id = (int) ($a['id_jadwal_karyawan'] ?? 0);
                $nama = (string) ($a['nama'] ?? $a['nama_jadwal'] ?? '');
                return [$id => $nama];
            })
            ->all();

        return $this->transaction->handleWithDataTable(
            fn() => $this->service->listQuery(
                $request->get('tanggal_mulai'),
                $request->get('tanggal_selesai'),
                $request->get('id_sdm') ? (int) $request->get('id_sdm') : null,
            ),
            [
                'action' => fn($row) => '
                    <div class="d-flex gap-1 ps-3">
                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                            onclick="openDetailAbsensi(' . $row->id_absensi . ')"><i class="bi bi-eye fs-4"></i></button>
                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm"
                            onclick="openEditAbsensi(' . $row->id_absensi . ')"><i class="bi bi-pencil fs-4"></i></button>
                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                            onclick="deleteAbsensi(' . $row->id_absensi . ')"><i class="bi bi-trash fs-4"></i></button>
                    </div>',
                'sdm' => fn($row) => $sdmMap[(int) $row->id_sdm] ?? ('SDM #' . $row->id_sdm),
                'jadwal' => fn($row) => $jadwalMap[(int) $row->id_jadwal_karyawan] ?? ('Jadwal #' . $row->id_jadwal_karyawan),
            ]
        );
    }

    public function show(string $id): JsonResponse
    {
        $bundle = $this->service->getShowBundle((int) $id);

        return $bundle
            ? $this->response->successResponse('OK', $bundle)
            : $this->response->errorResponse('Data absensi tidak ditemukan', 404);
    }

    public function store(AbsensiRequest $request): JsonResponse
    {
        return $this->transaction->handleWithTransactionOn('mysql', function () use ($request) {
            $data = $request->validated();
            $detailRows = $this->service->normalizeDetailRows($data['detail'] ?? []);
            $id = $this->service->create($data, $detailRows);

            return $this->response->successResponse('Absensi berhasil ditambahkan', ['id_absensi' => $id], 201);
        });
    }

    public function update(AbsensiRequest $request, string $id): JsonResponse
    {
        $absensi = $this->service->find((int) $id);
        if (!$absensi) return $this->response->errorResponse('Data absensi tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($request, $absensi) {
            $data = $request->validated();
            $detailRows = $this->service->normalizeDetailRows($data['detail'] ?? []);
            $this->service->update($absensi, $data, $detailRows);

            return $this->response->successResponse('Absensi berhasil diperbarui');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $absensi = $this->service->find((int) $id);
        if (!$absensi) return $this->response->errorResponse('Data absensi tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($absensi) {
            $this->service->delete($absensi);
            return $this->response->successResponse('Absensi berhasil dihapus');
        });
    }

    /**
     * Resolve jadwal master (id_jadwal) untuk SDM pada tanggal tertentu.
     * Digunakan frontend agar dropdown jadwal otomatis terisi sesuai tabel assignment sdm_jadwal_karyawan.
     */
    public function resolveJadwal(Request $request): JsonResponse
    {
        $idSdm = (int) ($request->get('id_sdm') ?? 0);
        $tanggal = (string) ($request->get('tanggal') ?? '');

        if (!$idSdm || !$tanggal) {
            return $this->response->errorResponse('Parameter id_sdm dan tanggal wajib diisi', 422);
        }

        $idJadwal = $this->service->resolveJadwalForSdm($idSdm, $tanggal);
        if (!$idJadwal) {
            return $this->response->successResponse('Jadwal belum ditugaskan untuk SDM pada tanggal tersebut', [
                'id_jadwal_karyawan' => null,
            ]);
        }

        // NOTE: kita kirim key id_jadwal_karyawan supaya frontend tetap kompatibel
        return $this->response->successResponse('OK', [
            'id_jadwal_karyawan' => $idJadwal,
        ]);
    }
    public function jadwalKaryawanOptions(Request $request): JsonResponse
{
    $data = $request->validate([
        'id_sdm' => ['required', 'integer'],
        'tanggal' => ['required', 'date'],
    ]);

    $options = $this->service->jadwalKaryawanOptionsForDate(
        (int) $data['id_sdm'],
        (string) $data['tanggal']
    );

    return $this->response->successResponse('OK', [
        'options' => $options
    ]);
}
public function jadwalKaryawanOptionsForDate(int $idSdm, string $tanggal): array
{
    return DB::connection('mysql')
        ->table('sdm_jadwal_karyawan as sjk')
        ->join('master_jadwal_kerja as mjk', 'mjk.id_jadwal', '=', 'sjk.id_jadwal')
        ->where('sjk.id_sdm', $idSdm)
        ->whereDate('sjk.tanggal_mulai', '<=', $tanggal)
        ->whereDate('sjk.tanggal_selesai', '>=', $tanggal)
        ->orderByDesc('sjk.tanggal_mulai')
        ->get([
            'sjk.id_jadwal_karyawan',
            'sjk.id_jadwal',
            'sjk.tanggal_mulai',
            'sjk.tanggal_selesai',
            'mjk.nama_jadwal',
            'mjk.jam_masuk',
            'mjk.jam_pulang',
        ])
        ->map(fn($r) => (array) $r)
        ->all();
}


}
