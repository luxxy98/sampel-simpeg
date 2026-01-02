<?php

namespace App\Http\Controllers\Admin\Absensi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Absensi\AbsenJenisRequest;
use App\Services\Absensi\AbsenJenisService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AbsenJenisController extends Controller
{
    public function __construct(
        private readonly AbsenJenisService $service,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
    {
        return view('admin.absensi.jenis.index');
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn () => $this->service->getListQuery(),
            [
                'action' => function ($row) {
                    // frontend kamu: openEditJenis(payload) & deleteJenis(id)
                    $payload = htmlspecialchars(json_encode([
                        'id_jenis_absen' => (int)$row->id_jenis_absen,
                        'nama_absen' => (string)$row->nama_absen,
                        'kategori' => (string)$row->kategori,
                        'potong_gaji' => (int)$row->potong_gaji,
                    ]), ENT_QUOTES, 'UTF-8');

                    $btnEdit = "<button type='button'
                        class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Edit' onclick='openEditJenis({$payload})'>
                        <span class='bi bi-pencil'></span>
                    </button>";

                    $btnDel = "<button type='button'
                        class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Hapus' onclick='deleteJenis(\"{$row->id_jenis_absen}\")'>
                        <span class='bi bi-trash'></span>
                    </button>";

                    return $btnEdit . ' ' . $btnDel;
                },
            ]
        );
    }

    public function show(string $id): JsonResponse
    {
        return $this->transactionService->handleWithShow(function () use ($id) {
            $row = $this->service->find((int)$id);
            if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);
            return $this->responseService->successResponse('OK', $row);
        });
    }

    public function store(AbsenJenisRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransactionOn('mysql', function () use ($request) {
            $data = $request->only(['nama_absen', 'kategori', 'potong_gaji']);
            if ($data['potong_gaji'] === null || $data['potong_gaji'] === '') $data['potong_gaji'] = 0;

            $this->service->create($data);
            return $this->responseService->successResponse('Jenis absen berhasil ditambahkan');
        });
    }

    public function update(AbsenJenisRequest $request, string $id): JsonResponse
    {
        $row = $this->service->find((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransactionOn('mysql', function () use ($request, $row) {
            $data = $request->only(['nama_absen', 'kategori', 'potong_gaji']);
            if ($data['potong_gaji'] === null || $data['potong_gaji'] === '') $data['potong_gaji'] = 0;

            $this->service->update($row, $data);
            return $this->responseService->successResponse('Jenis absen berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $row = $this->service->find((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransactionOn('mysql', function () use ($row) {
            $this->service->delete($row);
            return $this->responseService->successResponse('Jenis absen berhasil dihapus');
        });
    }
}
