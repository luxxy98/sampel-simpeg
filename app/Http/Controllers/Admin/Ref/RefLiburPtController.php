<?php

namespace App\Http\Controllers\Admin\Ref;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ref\RefLiburPtRequest;
use App\Services\Ref\RefLiburPtService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class RefLiburPtController extends Controller
{
    public function __construct(
        private readonly RefLiburPtService $refLiburPtService,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    )
    {
    }

    public function index(): View
    {
        return view('admin.ref.libur_pt.index');
    }

    public function list(): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            function () {
                return $this->refLiburPtService->getListData();
            },
            [
                'action' => function ($row) {
                    $rowId = $row->id_libur_pt;

                    return implode(' ', [
                        $this->transactionService->actionButton($rowId, 'detail'),
                        $this->transactionService->actionButton($rowId, 'edit'),
                        $this->transactionService->actionButton($rowId, 'delete'),
                    ]);
                },
            ]
        );
    }

    public function store(RefLiburPtRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $this->refLiburPtService->create($request->only([
                'tanggal',
                'keterangan',
            ]));

            return $this->responseService->successResponse('Data berhasil disimpan');
        });
    }

    public function show(string $id): JsonResponse
    {
        return $this->transactionService->handleWithShow(function () use ($id) {
            $data = $this->refLiburPtService->getDetailData($id);

            return $this->responseService->successResponse('Data berhasil diambil', $data);
        });
    }

    public function update(RefLiburPtRequest $request, string $id): JsonResponse
    {
        $data = $this->refLiburPtService->findById($id);
        if (!$data) {
            return $this->responseService->errorResponse('Data tidak ditemukan');
        }

        return $this->transactionService->handleWithTransaction(function () use ($request, $data) {
            $this->refLiburPtService->update($data, $request->only([
                'tanggal',
                'keterangan',
            ]));

            return $this->responseService->successResponse('Data berhasil diperbarui');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $data = $this->refLiburPtService->findById($id);
        if (!$data) {
            return $this->responseService->errorResponse('Data tidak ditemukan');
        }

        return $this->transactionService->handleWithTransaction(function () use ($data) {
            $this->refLiburPtService->delete($data);

            return $this->responseService->successResponse('Data berhasil dihapus');
        });
    }
}
