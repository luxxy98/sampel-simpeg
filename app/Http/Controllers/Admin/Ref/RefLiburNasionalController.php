<?php

namespace App\Http\Controllers\Admin\Ref;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ref\RefLiburNasionalRequest;
use App\Services\Ref\RefLiburNasionalService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class RefLiburNasionalController extends Controller
{
    public function __construct(
        private readonly RefLiburNasionalService $refLiburNasionalService,
        private readonly TransactionService      $transactionService,
        private readonly ResponseService         $responseService,
    )
    {
    }

    public function index(): View
    {
        return view('admin.ref.libur_nasional.index');
    }

    public function list(): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            function () {
                return $this->refLiburNasionalService->getListData();
            },
            [
                'action' => function ($row) {
                    $rowId = $row->id_kalnas;

                    return implode(' ', [
                        $this->transactionService->actionButton($rowId, 'detail'),
                        $this->transactionService->actionButton($rowId, 'edit'),
                        $this->transactionService->actionButton($rowId, 'delete'),
                    ]);
                },
            ]
        );
    }

    public function store(RefLiburNasionalRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $this->refLiburNasionalService->create($request->only([
                'tanggal',
                'keterangan',
            ]));

            return $this->responseService->successResponse('Data berhasil disimpan');
        });
    }

    public function show(string $id): JsonResponse
    {
        return $this->transactionService->handleWithShow(function () use ($id) {
            $data = $this->refLiburNasionalService->getDetailData($id);

            return $this->responseService->successResponse('Data berhasil diambil', $data);
        });
    }

    public function update(RefLiburNasionalRequest $request, string $id): JsonResponse
    {
        $data = $this->refLiburNasionalService->findById($id);
        if (!$data) {
            return $this->responseService->errorResponse('Data tidak ditemukan');
        }

        return $this->transactionService->handleWithTransaction(function () use ($request, $data) {
            $this->refLiburNasionalService->update($data, $request->only([
                'tanggal',
                'keterangan',
            ]));

            return $this->responseService->successResponse('Data berhasil diperbarui');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $data = $this->refLiburNasionalService->findById($id);
        if (!$data) {
            return $this->responseService->errorResponse('Data tidak ditemukan');
        }

        return $this->transactionService->handleWithTransaction(function () use ($data) {
            $this->refLiburNasionalService->delete($data);

            return $this->responseService->successResponse('Data berhasil dihapus');
        });
    }
}
