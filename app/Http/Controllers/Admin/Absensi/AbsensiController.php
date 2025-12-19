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
        private readonly AbsensiService $absensiService,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
    {
        return view('admin.absensi.index', [
            'sdms' => $this->absensiService->getSdmDropdown(),
            'jadwals' => $this->absensiService->getJadwalDropdown(),
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn() => $this->absensiService->getListData($request),
            [
                'aksi' => function ($row) {
                    $id = (int) $row->id_absensi;
                    return implode(' ', [
                        "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1' title='Detail' onclick='openDetailAbsensi($id)'><span class='bi bi-file-text'></span></button>",
                        "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1' title='Edit' onclick='openEditAbsensi($id)'><span class='bi bi-pencil'></span></button>",
                        "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-danger btn-sm m-1' title='Hapus' onclick='deleteAbsensi($id)'><span class='bi bi-trash'></span></button>",
                    ]);
                },

            ]
        );
    }

    public function store(AbsensiRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $data = $this->absensiService->create($request->validated());
            return $this->responseService->successResponse('Data absensi berhasil dibuat', $data, 201);
        });
    }

    // dipakai openEditAbsensi(id) -> butuh JSON tanpa wrapper
    public function show(string $id): JsonResponse
    {
        $data = $this->absensiService->getEditData($id);
        if (!$data) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        return response()->json($data);
    }

    public function update(AbsensiRequest $request, string $id): JsonResponse
    {
        $absensi = $this->absensiService->findById($id);
        if (!$absensi) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransaction(function () use ($request, $absensi) {
            $updated = $this->absensiService->update($absensi, $request->validated());
            return $this->responseService->successResponse('Data absensi berhasil diperbarui', $updated);
        });
    }

    // dipakai openDetailAbsensi(id) -> struktur JSON khusus
    public function detail(string $id): JsonResponse
    {
        $payload = $this->absensiService->getDetailPayload($id);
        if (!$payload) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        return response()->json($payload);
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $deleted = $this->absensiService->deleteById($id);

            if (!$deleted) {
                return $this->responseService->errorResponse('Data absensi tidak ditemukan', 404);
            }

            return $this->responseService->successResponse('Berhasil menghapus data absensi');
        });
    }
}
