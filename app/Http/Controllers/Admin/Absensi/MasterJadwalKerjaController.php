<?php

namespace App\Http\Controllers\Admin\Absensi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Absensi\MasterJadwalKerjaRequest;
use App\Services\Absensi\MasterJadwalKerjaService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class MasterJadwalKerjaController extends Controller
{
    public function __construct(
        private readonly MasterJadwalKerjaService $service,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        return view('admin.absensi.jadwal_kerja.index');
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn () => $this->service->getListQuery(),
            [
                'action' => function ($row) {
                    $payload = htmlspecialchars(json_encode([
                        'id_jadwal' => (int) $row->id_jadwal,
                        'nama_jadwal' => (string) $row->nama_jadwal,
                        'keterangan' => (string) ($row->keterangan ?? ''),
                        'jam_masuk' => (string) $row->jam_masuk,
                        'jam_pulang' => (string) $row->jam_pulang,
                    ]), ENT_QUOTES, 'UTF-8');

                    $btnEdit = "<button type='button'
                        class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Edit' onclick='openEditJadwalKerja({$payload})'>
                        <span class='bi bi-pencil'></span>
                    </button>";

                    $btnDel = "<button type='button'
                        class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Hapus' onclick='deleteJadwalKerja(\"{$row->id_jadwal}\")'>
                        <span class='bi bi-trash'></span>
                    </button>";

                    return $btnEdit . ' ' . $btnDel;
                },
            ]
        );
    }

    public function show(string $id): JsonResponse
    {
        return $this->transaction->handleWithShow(function () use ($id) {
            $row = $this->service->find((int) $id);
            if (!$row) return $this->response->errorResponse('Data tidak ditemukan', 404);
            return $this->response->successResponse('OK', $row);
        });
    }

    public function store(MasterJadwalKerjaRequest $request): JsonResponse
    {
        return $this->transaction->handleWithTransactionOn('mysql', function () use ($request) {
            $data = $request->only(['nama_jadwal', 'keterangan', 'jam_masuk', 'jam_pulang']);
            $this->service->create($data);
            return $this->response->successResponse('Master jadwal kerja berhasil ditambahkan');
        });
    }

    public function update(MasterJadwalKerjaRequest $request, string $id): JsonResponse
    {
        $row = $this->service->find((int) $id);
        if (!$row) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($request, $row) {
            $data = $request->only(['nama_jadwal', 'keterangan', 'jam_masuk', 'jam_pulang']);
            $this->service->update($row, $data);
            return $this->response->successResponse('Master jadwal kerja berhasil diupdate');
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $row = $this->service->find((int) $id);
        if (!$row) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($row) {
            $this->service->delete($row);
            return $this->response->successResponse('Master jadwal kerja berhasil dihapus');
        });
    }
}
