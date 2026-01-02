<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gaji\GajiJenisKomponenRequest;
use App\Services\Gaji\GajiJenisKomponenService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GajiJenisKomponenController extends Controller
{
    public function __construct(
        private readonly GajiJenisKomponenService $service,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function list(Request $request): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn () => $this->service->getListQuery(),
            [
                'action' => function ($row) {
                    $payload = htmlspecialchars(json_encode([
                        'id_jenis_komponen' => (int)$row->id_jenis_komponen,
                        'nama_komponen' => $row->nama_komponen,
                        'jenis' => $row->jenis,
                    ]), ENT_QUOTES, 'UTF-8');

                    $btnEdit = "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Edit' onclick='openEditJenisKomponen({$payload})'>
                        <span class='bi bi-pencil'></span>
                    </button>";

                    $btnDel = "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Hapus' onclick='deleteJenisKomponen(\"{$row->id_jenis_komponen}\")'>
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
            $row = $this->service->getDetail((int)$id);
            if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);
            return $this->responseService->successResponse('OK', $row);
        });
    }

    public function store(GajiJenisKomponenRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $this->service->create($request->only(['nama_komponen', 'jenis']));
            return $this->responseService->successResponse('Jenis komponen berhasil ditambahkan');
        });
    }

    public function update(GajiJenisKomponenRequest $request, string $id): JsonResponse
    {
        $row = $this->service->getDetail((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransaction(function () use ($request, $row) {
            $this->service->update($row, $request->only(['nama_komponen', 'jenis']));
            return $this->responseService->successResponse('Jenis komponen berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $row = $this->service->getDetail((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransaction(function () use ($row) {
            $this->service->delete($row);
            return $this->responseService->successResponse('Jenis komponen berhasil dihapus');
        });
    }
}
