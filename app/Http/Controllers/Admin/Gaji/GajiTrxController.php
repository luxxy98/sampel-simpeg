<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Services\Gaji\GajiTrxService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GajiTrxController extends Controller
{
    public function __construct(
        private readonly GajiTrxService $service,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
{
    return view('admin.gaji.trx.index', [
        // Blade memakai akses array: $p['id_periode']
        'periodeOptions' => $this->service->periodeOptions()->map(fn ($r) => (array) $r)->all(),
    ]);
}


    public function list(Request $request): JsonResponse
    {
        $idPeriode = $request->get('id_periode') ? (int)$request->get('id_periode') : null;
        $status = $request->get('status') ?: null;

        return $this->transactionService->handleWithDataTable(
            fn () => $this->service->getListQuery($idPeriode, $status),
            [
                'action' => function ($row) {
                    $id = (int)$row->id_gaji;

                    return "
                        <div class='d-flex gap-1 ps-3'>
                            <button type='button'
                                class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                                title='Detail'
                                onclick='openDetailGaji({$id})'>
                                <span class='bi bi-file-text'></span>
                            </button>
                        </div>
                    ";
                },
                'status' => function ($row) {
                    $status = $row->status;
                    
                    // Handle NULL status
                    if ($status === null || $status === '') {
                        return "<span class='badge bg-secondary'>-</span>";
                    }
                    
                    // Format badge based on status
                    $badgeClass = match($status) {
                        'DISETUJUI' => 'bg-success',
                        'DRAFT' => 'bg-warning text-dark',
                        'DIBATALKAN' => 'bg-danger',
                        default => 'bg-secondary',
                    };
                    
                    return "<span class='badge {$badgeClass}'>{$status}</span>";
                },
            ]
        );
    }

    public function show(string $id): JsonResponse
    {
        return $this->transactionService->handleWithShow(function () use ($id) {
            $bundle = $this->service->getDetailBundle((int)$id);

            if (!$bundle) {
                return $this->responseService->errorResponse('Transaksi gaji tidak ditemukan', 404);
            }

            return $this->responseService->successResponse('OK', $bundle);
        });
    }
}
