<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gaji\GajiPeriodeRequest;
use App\Services\Gaji\GajiPeriodeService;
use App\Services\Gaji\GajiGenerateService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GajiPeriodeController extends Controller
{
    public function __construct(
        private readonly GajiPeriodeService $service,
        private readonly GajiGenerateService $generateService,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
    {
        return view('admin.gaji.periode.index');
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn () => $this->service->getListQuery(),
            [
                'action' => function ($row) {
                    $id = (int) $row->id_periode;

                    return '
                        <div class="d-flex gap-1 ps-3">
                            <button type="button" class="btn btn-sm btn-icon btn-light-success"
                                onclick="generateGaji(' . $id . ')" title="Generate Gaji">
                                <i class="ki-outline ki-rocket fs-2 text-success"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-icon btn-light-info"
                                onclick="openDetailPeriode(' . $id . ')" title="Detail">
                                <i class="ki-outline ki-eye fs-2 text-info"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-icon btn-light-warning"
                                onclick="openEditPeriode(' . $id . ')" title="Edit">
                                <i class="ki-outline ki-pencil fs-2 text-warning"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-icon btn-light-danger"
                                onclick="deletePeriode(' . $id . ')" title="Hapus">
                                <i class="ki-outline ki-trash fs-2 text-danger"></i>
                            </button>
                        </div>
                    ';
                },
            ]
        );
    }

    public function show(string $id): JsonResponse
    {
        $row = $this->service->find((int) $id);
        if (!$row) {
            return $this->responseService->errorResponse('Data periode tidak ditemukan', 404);
        }

        return $this->responseService->successResponse('OK', $row);
    }

    public function store(GajiPeriodeRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $data = $request->only([
                'tahun',
                'bulan',
                'tanggal_mulai',
                'tanggal_selesai',
                'tanggal_penggajian',
                'status',
                'status_peninjauan',
            ]);

            $this->service->create($data);

            return $this->responseService->successResponse('Periode gaji berhasil ditambahkan');
        });
    }

    public function update(GajiPeriodeRequest $request, string $id): JsonResponse
    {
        $id = (int) $id;

        return $this->transactionService->handleWithTransaction(function () use ($request, $id) {
            $data = $request->only([
                'tahun',
                'bulan',
                'tanggal_mulai',
                'tanggal_selesai',
                'tanggal_penggajian',
                'status',
                'status_peninjauan',
            ]);

            $ok = $this->service->update($id, $data);
            if (!$ok) {
                return $this->responseService->errorResponse('Data periode tidak ditemukan', 404);
            }

            return $this->responseService->successResponse('Periode gaji berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($id) {
            $ok = $this->service->delete((int) $id);

            if (!$ok) {
                return $this->responseService->errorResponse('Data periode tidak ditemukan', 404);
            }

            return $this->responseService->successResponse('Periode gaji berhasil dihapus');
        });
    }

    public function generate(string $id): JsonResponse
    {
        return $this->transactionService->handleWithTransactionOn('absensigaji', function () use ($id) {
            $result = $this->generateService->generateForPeriode((int) $id);

            $msg = "Generate selesai: {$result['success']} pegawai berhasil, {$result['error']} gagal.";

            return $this->responseService->successResponse($msg, $result);
        });
    }
}
