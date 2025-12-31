<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gaji\GajiDistribusiRequest;
use App\Models\Gaji\GajiDistribusi;
use App\Services\Gaji\GajiDistribusiService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GajiDistribusiController extends Controller
{
    public function __construct(
        private readonly GajiDistribusiService $service,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
    {
        return view('admin.gaji.distribusi.index', [
            'periodeOptions' => $this->service->periodeOptions()->map(fn($r) => (array) $r)->all(),
            'sdmOptions' => $this->service->sdmOptions()->map(fn($r) => (array) $r)->all(),
            'rekeningOptions' => $this->service->rekeningOptions()->map(fn($r) => (array) $r)->all(),
            'trxOptions' => $this->service->trxOptions()->map(fn($r) => (array) $r)->all(),
        ]);
    }


    public function list(Request $request): JsonResponse
    {
        $idPeriode = $request->get('id_periode') ? (int)$request->get('id_periode') : null;
        $status = $request->get('status_transfer') ?: null;

        return $this->transactionService->handleWithDataTable(
            fn() => $this->service->getListQuery($idPeriode, $status),
            [
                'action' => function ($row) {
                    $payload = htmlspecialchars(json_encode([
                        'id_distribusi' => (int)$row->id_distribusi,
                        'id_periode' => (int)$row->id_periode,
                        'id_gaji' => (int)$row->id_gaji,
                        'id_sdm' => (int)$row->id_sdm,
                        'id_rekening' => $row->id_rekening !== null ? (int)$row->id_rekening : null,
                        'jumlah_transfer' => (string)$row->jumlah_transfer,
                        'status_transfer' => (string)$row->status_transfer,
                        'tanggal_transfer' => $row->tanggal_transfer ? (string)$row->tanggal_transfer : null,
                        'catatan' => $row->catatan,
                    ]), ENT_QUOTES, 'UTF-8');

                    return "
                        <div class='d-flex gap-1 ps-3'>
                            <button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                                title='Detail' onclick='openDetailDistribusi({$row->id_distribusi})'>
                                <span class='bi bi-file-text'></span>
                            </button>

                            <button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                                title='Edit' onclick='openEditDistribusi({$payload})'>
                                <span class='bi bi-pencil'></span>
                            </button>

                            <button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                                title='Hapus' onclick='deleteDistribusi(\"{$row->id_distribusi}\")'>
                                <span class='bi bi-trash'></span>
                            </button>
                        </div>
                    ";
                },
            ]
        );
    }

    public function show(string $id): JsonResponse
    {
        return $this->transactionService->handleWithShow(function () use ($id) {
            $data = $this->service->getDetailData((int)$id);
            if (!$data) return $this->responseService->errorResponse('Data distribusi tidak ditemukan', 404);
            return $this->responseService->successResponse('OK', $data);
        });
    }

    public function store(GajiDistribusiRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $data = $request->only([
                'id_periode',
                'id_gaji',
                'id_sdm',
                'id_rekening',
                'jumlah_transfer',
                'status_transfer',
                'tanggal_transfer',
                'catatan',
            ]);

            // agar default DB bekerja (tanggal_transfer default CURRENT_TIMESTAMP)
            if (empty($data['tanggal_transfer'])) unset($data['tanggal_transfer']);
            if ($data['id_rekening'] === null || $data['id_rekening'] === '') $data['id_rekening'] = null;
            if ($data['catatan'] === '') $data['catatan'] = null;

            GajiDistribusi::create($data);

            return $this->responseService->successResponse('Distribusi transfer berhasil ditambahkan');
        });
    }

    public function update(GajiDistribusiRequest $request, string $id): JsonResponse
    {
        $row = GajiDistribusi::find((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data distribusi tidak ditemukan', 404);

        return $this->transactionService->handleWithTransaction(function () use ($request, $row) {
            $data = $request->only([
                'id_periode',
                'id_gaji',
                'id_sdm',
                'id_rekening',
                'jumlah_transfer',
                'status_transfer',
                'tanggal_transfer',
                'catatan',
            ]);

            if (empty($data['tanggal_transfer'])) unset($data['tanggal_transfer']);
            if ($data['id_rekening'] === null || $data['id_rekening'] === '') $data['id_rekening'] = null;
            if ($data['catatan'] === '') $data['catatan'] = null;

            $row->update($data);

            return $this->responseService->successResponse('Distribusi transfer berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $row = GajiDistribusi::find((int)$id);
        if (!$row) return $this->responseService->errorResponse('Data distribusi tidak ditemukan', 404);

        return $this->transactionService->handleWithTransaction(function () use ($row) {
            $row->delete();
            return $this->responseService->successResponse('Distribusi transfer berhasil dihapus');
        });
    }
}
