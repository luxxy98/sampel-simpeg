<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gaji\PeriodeGajiRequest;
use App\Services\Gaji\PeriodeGajiService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PeriodeGajiController extends Controller
{
    public function __construct(
        private readonly PeriodeGajiService $periodeGajiService,
        private readonly TransactionService $transactionService,
        private readonly ResponseService $responseService,
    ) {}

    public function index(): View
    {
        return view('admin.gaji.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn() => $this->periodeGajiService->getListData($request),
            [
                'aksi' => function ($row) {
                    $id = (int) $row->id_periode;
                    return "<button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1' title='Edit' onclick='openEditPeriode($id)'><span class='bi bi-pencil'></span></button>";
                },
            ]
        );
    }

    public function store(PeriodeGajiRequest $request): JsonResponse
    {
        return $this->transactionService->handleWithTransaction(function () use ($request) {
            $data = $this->periodeGajiService->create($request->validated());
            return $this->responseService->successResponse('Periode gaji berhasil dibuat', $data, 201);
        });
    }

    // dipakai openEditPeriode(id) -> JSON tanpa wrapper
    public function show(string $id): JsonResponse
    {
        $data = $this->periodeGajiService->getEditData($id);
        if (!$data) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        return response()->json($data);
    }

    public function update(PeriodeGajiRequest $request, string $id): JsonResponse
    {
        $periode = $this->periodeGajiService->findById($id);
        if (!$periode) return $this->responseService->errorResponse('Data tidak ditemukan', 404);

        return $this->transactionService->handleWithTransaction(function () use ($request, $periode) {
            $updated = $this->periodeGajiService->update($periode, $request->validated());
            return $this->responseService->successResponse('Periode gaji berhasil diperbarui', $updated);
        });
    }
}
