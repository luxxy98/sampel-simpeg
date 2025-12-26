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
use Illuminate\Support\Facades\DB;

final class AbsensiController extends Controller
{
    public function __construct(
        private readonly AbsensiService $absensiService,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
    {
        // Blade di modul Absensi mengakses opsi dengan array syntax ($opt['id_sdm']),
        // jadi pastikan kita kirim array (bukan stdClass).
        $sdmOptions = $this->absensiService
            ->sdmOptions()
            ->map(fn ($r) => (array) $r)
            ->all();

        $jadwalOptions = $this->absensiService
            ->jadwalOptions()
            ->all();

        $jenisAbsenOptions = $this->absensiService
            ->jenisAbsenOptions()
            ->map(fn ($r) => (array) $r)
            ->all();

        return view('admin.absensi.index', compact('sdmOptions', 'jadwalOptions', 'jenisAbsenOptions'));
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn () => $this->absensiService->getListData(
                $request->get('tanggal_mulai'),
                $request->get('tanggal_selesai'),
                $request->get('id_sdm') ? (int) $request->get('id_sdm') : null,
            ),
            [
                'action' => fn ($row) => '
                    <div class="d-flex gap-1 ps-3">
                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                            onclick="openDetailAbsensi(' . $row->id_absensi . ')"><i class="bi bi-eye fs-4"></i></button>
                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm"
                            onclick="openEditAbsensi(' . $row->id_absensi . ')"><i class="bi bi-pencil fs-4"></i></button>
                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                            onclick="deleteAbsensi(' . $row->id_absensi . ')"><i class="bi bi-trash fs-4"></i></button>
                    </div>',
            ],
        );
    }

    public function show(string $id): JsonResponse
    {
        $bundle = $this->absensiService->getDetailBundle($id);

        return $bundle
            ? $this->responseService->successResponse('OK', $bundle)
            : $this->responseService->errorResponse('Data absensi tidak ditemukan', 404);
    }

    public function store(AbsensiRequest $request): JsonResponse
    {
        return DB::connection('absensi')->transaction(function () use ($request) {
            $this->absensiService->create(
                $request->only([
                    'tanggal',
                    'id_jadwal_karyawan',
                    'id_sdm',
                    'total_jam_kerja',
                    'total_terlambat',
                    'total_pulang_awal',
                ]),
                $this->buildDetailRows($request->input('detail', [])),
            );

            return $this->responseService->successResponse('Absensi berhasil ditambahkan');
        });
    }

    public function update(AbsensiRequest $request, string $id): JsonResponse
    {
        $absensi = $this->absensiService->findById($id);
        if (! $absensi) {
            return $this->responseService->errorResponse('Data tidak ditemukan', 404);
        }

        return DB::connection('absensi')->transaction(function () use ($request, $absensi) {
            $this->absensiService->update(
                $absensi,
                $request->only([
                    'tanggal',
                    'id_jadwal_karyawan',
                    'id_sdm',
                    'total_jam_kerja',
                    'total_terlambat',
                    'total_pulang_awal',
                ]),
                $this->buildDetailRows($request->input('detail', [])),
            );

            return $this->responseService->successResponse('Absensi berhasil diperbarui');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $absensi = $this->absensiService->findById($id);
        if (! $absensi) {
            return $this->responseService->errorResponse('Data tidak ditemukan', 404);
        }

        $this->absensiService->delete($absensi);

        return $this->responseService->successResponse('Absensi berhasil dihapus');
    }

    private function buildDetailRows(array $detail): array
    {
        $rows = [];
        $n = count($detail['id_jenis_absen'] ?? []);

        for ($i = 0; $i < $n; $i++) {
            if (empty($detail['id_jenis_absen'][$i])) {
                continue;
            }

            $rows[] = [
                'id_jenis_absen' => (int) $detail['id_jenis_absen'][$i],
                'waktu_mulai' => $detail['waktu_mulai'][$i] ?? null,
                'waktu_selesai' => $detail['waktu_selesai'][$i] ?? null,
                'durasi_jam' => $detail['durasi_jam'][$i] ?? 0,
                'lokasi_pulang' => $detail['lokasi_pulang'][$i] ?? null,
            ];
        }

        return $rows;
    }
}
