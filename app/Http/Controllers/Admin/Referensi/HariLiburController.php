<?php

namespace App\Http\Controllers\Admin\Referensi;

use App\Http\Controllers\Controller;
use App\Services\Referensi\HariLiburService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class HariLiburController extends Controller
{
    public function __construct(
        private readonly HariLiburService $service,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        return view('admin.referensi.hari-libur.index');
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
                                onclick=\'openEditHariLibur(' . json_encode([
                                    'id_hari_libur' => $row->id_hari_libur,
                                    'tanggal' => $row->tanggal,
                                    'nama' => $row->nama,
                                    'keterangan' => $row->keterangan,
                                ]) . ')\'><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                onclick="deleteHariLibur(' . $row->id_hari_libur . ')"><i class="bi bi-trash"></i></button>
                        </div>';
                },
                'tanggal' => fn($row) => date('d/m/Y', strtotime($row->tanggal)),
            ]
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal',
            'nama' => 'required|string|max:120',
            'keterangan' => 'nullable|string|max:255',
        ]);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($data) {
            $this->service->create($data);
            return $this->response->successResponse('Hari libur berhasil ditambahkan');
        });
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $libur = $this->service->find((int) $id);
        if (!$libur) return $this->response->errorResponse('Data tidak ditemukan', 404);

        $data = $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal,' . $id . ',id_hari_libur',
            'nama' => 'required|string|max:120',
            'keterangan' => 'nullable|string|max:255',
        ]);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($libur, $data) {
            $this->service->update($libur, $data);
            return $this->response->successResponse('Hari libur berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $libur = $this->service->find((int) $id);
        if (!$libur) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($libur) {
            $this->service->delete($libur);
            return $this->response->successResponse('Hari libur berhasil dihapus');
        });
    }
}
