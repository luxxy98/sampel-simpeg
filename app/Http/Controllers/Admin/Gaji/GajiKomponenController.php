<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gaji\GajiKomponenRequest;
use App\Services\Gaji\GajiKomponenService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GajiKomponenController extends Controller
{
    public function __construct(
        private readonly GajiKomponenService $service,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
    {
        return view('admin.gaji.komponen.index', [
            'jabatanOptions' => $this->service->jabatanOptions()->map(fn($r) => (array) $r)->all(),
            'jenisKomponenOptions' => $this->service->jenisKomponenOptions()->map(fn($r) => (array) $r)->all(),
        ]);
    }


    public function list(Request $request): JsonResponse
    {
        $idJabatan = $request->get('id_jabatan') ? (int)$request->get('id_jabatan') : null;
        $jenis = $request->get('jenis') ?: null;

        return $this->transactionService->handleWithDataTable(
            fn() => $this->service->getListQuery($idJabatan, $jenis),
            [
                'action' => function ($row) {
                    $payload = htmlspecialchars(json_encode([
                        'id_gaji_komponen' => (int)$row->id_gaji_komponen,
                        'id_jabatan' => (int)$row->id_jabatan,
                        'id_jenis_komponen' => (int)$row->id_jenis_komponen,
                        'nominal' => (string)$row->nominal,
                    ]), ENT_QUOTES, 'UTF-8');

                    $btnEdit = "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Edit' onclick='openEditKomponenGaji({$payload})'>
                        <span class='bi bi-pencil'></span>
                    </button>";

                    $btnDel = "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Hapus' onclick='deleteKomponenGaji(\"{$row->id_gaji_komponen}\")'>
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
            $row = $this->service->getDetailRow((int)$id);
            if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);
            return $this->responseService->successResponse('OK', $row);
        });
    }

    public function store(GajiKomponenRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransactionOn('absensigaji', function () use ($request) {
            $this->service->create($request->only(['id_jabatan', 'id_jenis_komponen', 'nominal']));
            return $this->responseService->successResponse('Komponen gaji berhasil ditambahkan');
        });
    }

    public function update(GajiKomponenRequest $request, string $id): JsonResponse
    {
        $row = $this->service->findById((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransactionOn('absensigaji', function () use ($request, $row) {
            $this->service->update($row, $request->only(['id_jabatan', 'id_jenis_komponen', 'nominal']));
            return $this->responseService->successResponse('Komponen gaji berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $row = $this->service->findById((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransactionOn('absensigaji', function () use ($row) {
            $this->service->delete($row);
            return $this->responseService->successResponse('Komponen gaji berhasil dihapus');
        });
    }
}
