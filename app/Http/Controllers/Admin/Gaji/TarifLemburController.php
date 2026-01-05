<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Services\Gaji\TarifLemburService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TarifLemburController extends Controller
{
    public function __construct(
        private readonly TarifLemburService $service,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        return view('admin.gaji.tarif-lembur.index');
    }

    public function list(): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn() => $this->service->getListQuery(),
            [
                'action' => function ($row) {
                    return '
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm"
                                onclick=\'openEditTarifLembur(' . json_encode([
                                    'id_tarif' => $row->id_tarif,
                                    'nama_tarif' => $row->nama_tarif,
                                    'tarif_per_jam' => $row->tarif_per_jam,
                                    'keterangan' => $row->keterangan,
                                ]) . ')\'><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                onclick="deleteTarifLembur(' . $row->id_tarif . ')"><i class="bi bi-trash"></i></button>
                        </div>';
                },
                'tarif_per_jam' => fn($row) => 'Rp ' . number_format($row->tarif_per_jam, 0, ',', '.'),
            ]
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_tarif' => 'required|string|max:100',
            'tarif_per_jam' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($data) {
            $this->service->create($data);
            return $this->response->successResponse('Tarif lembur berhasil ditambahkan');
        });
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $tarif = $this->service->find((int) $id);
        if (!$tarif) return $this->response->errorResponse('Data tidak ditemukan', 404);

        $data = $request->validate([
            'nama_tarif' => 'required|string|max:100',
            'tarif_per_jam' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($tarif, $data) {
            $this->service->update($tarif, $data);
            return $this->response->successResponse('Tarif lembur berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $tarif = $this->service->find((int) $id);
        if (!$tarif) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($tarif) {
            $this->service->delete($tarif);
            return $this->response->successResponse('Tarif lembur berhasil dihapus');
        });
    }
}
