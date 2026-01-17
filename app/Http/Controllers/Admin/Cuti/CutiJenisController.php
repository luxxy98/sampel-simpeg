<?php

namespace App\Http\Controllers\Admin\Cuti;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cuti\CutiJenisRequest;
use App\Services\Cuti\CutiJenisService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class CutiJenisController extends Controller
{
    public function __construct(
        private readonly CutiJenisService $service,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        return view('admin.cuti.jenis.index');
    }

    public function list(): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn() => $this->service->getListData(),
            [
                'action' => function ($row) {
                    $id = $row->id_jenis_cuti;
                    return implode(' ', [
                        $this->transaction->actionButton($id, 'detail'),
                        $this->transaction->actionButton($id, 'edit'),
                        $this->transaction->actionButton($id, 'delete'),
                    ]);
                },
            ]
        );
    }

    public function store(CutiJenisRequest $request): JsonResponse
    {
        return $this->transaction->handleWithTransaction(function () use ($request) {
            $data = $this->service->create($request->only([
                'nama_jenis',
                'maks_hari_per_tahun',
                'status',
            ]));

            return $this->response->successResponse('Jenis cuti berhasil dibuat', $data, 201);
        });
    }

    public function show(string $id): JsonResponse
    {
        return $this->transaction->handleWithShow(function () use ($id) {
            $data = $this->service->getDetailData($id);
            return $data
                ? $this->response->successResponse('Data berhasil diambil', $data)
                : $this->response->errorResponse('Data tidak ditemukan', 404);
        });
    }

    public function update(CutiJenisRequest $request, string $id): JsonResponse
    {
        $jenis = $this->service->findById($id);
        if (!$jenis) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransaction(function () use ($request, $jenis) {
            $updated = $this->service->update($jenis, $request->only([
                'nama_jenis',
                'maks_hari_per_tahun',
                'status',
            ]));

            return $this->response->successResponse('Jenis cuti berhasil diperbarui', $updated);
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $jenis = $this->service->findById($id);
        if (!$jenis) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransaction(function () use ($jenis) {
            $this->service->delete($jenis);
            return $this->response->successResponse('Jenis cuti berhasil dihapus');
        });
    }
}
