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
        $tarifLemburOptions = $this->service->tarifLemburOptions()->map($toPlainArray)->all();
        $holidayDates = $this->service->getHolidayDates()->all();

        return view('admin.absensi.index', compact(
            'sdmOptions', 
            'jadwalOptions', 
            'jenisAbsenOptions',
            'tarifLemburOptions',
            'holidayDates'
        ));
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
     * Check if a date is a holiday (AJAX endpoint)
     */
    public function checkHoliday(Request $request): JsonResponse
    {
        $tanggal = $request->get('tanggal');
        if (!$tanggal) {
            return $this->response->errorResponse('Tanggal diperlukan', 400);
        }

        $info = $this->service->getHolidayInfo($tanggal);
        return $this->response->successResponse('OK', $info);
    }
}
