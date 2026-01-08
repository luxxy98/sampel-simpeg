<?php

namespace App\Http\Controllers\Admin\Absensi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Absensi\JadwalKaryawanRequest;
use App\Services\Absensi\AbsensiService;
use App\Services\Absensi\JadwalKaryawanService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class JadwalKaryawanController extends Controller
{
    public function __construct(
        private readonly JadwalKaryawanService $service,
        private readonly AbsensiService $absensiService,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        $toPlainArray = static fn($r) => is_array($r) ? $r : (method_exists($r, 'toArray') ? $r->toArray() : (array) $r);

        $sdmOptions = $this->absensiService->sdmOptions()->map($toPlainArray)->all();
        $jadwalOptions = $this->absensiService->jadwalOptions()->map($toPlainArray)->all();

        return view('admin.absensi.jadwal_karyawan.index', compact('sdmOptions', 'jadwalOptions'));
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn () => $this->service->getListQuery(),
            [
                'action' => function ($row) {
                    $payload = htmlspecialchars(json_encode([
                        'id_jadwal_karyawan' => (int) $row->id_jadwal_karyawan,
                        'id_sdm' => (int) $row->id_sdm,
                        'id_jadwal' => (int) $row->id_jadwal,
                        'tanggal_mulai' => (string) $row->tanggal_mulai,
                        'tanggal_selesai' => (string) $row->tanggal_selesai,
                        'dibuat_oleh' => $row->dibuat_oleh,
                    ]), ENT_QUOTES, 'UTF-8');

                    $btnEdit = "<button type='button'
                        class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Edit' onclick='openEditJadwalKaryawan({$payload})'>
                        <span class='bi bi-pencil'></span>
                    </button>";

                    $btnDel = "<button type='button'
                        class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm m-1'
                        title='Hapus' onclick='deleteJadwalKaryawan(\"{$row->id_jadwal_karyawan}\")'>
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

    public function store(JadwalKaryawanRequest $request): JsonResponse
    {
        return $this->transaction->handleWithTransactionOn('mysql', function () use ($request) {
            $data = $request->only(['id_sdm', 'id_jadwal', 'tanggal_mulai', 'tanggal_selesai', 'dibuat_oleh']);
            if (empty($data['dibuat_oleh'])) {
                $data['dibuat_oleh'] = $this->defaultDibuatOleh();
            }
            $this->service->create($data);
            return $this->response->successResponse('Jadwal karyawan berhasil ditambahkan');
        });
    }

    public function update(JadwalKaryawanRequest $request, string $id): JsonResponse
    {
        $row = $this->service->find((int) $id);
        if (!$row) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($request, $row) {
            $data = $request->only(['id_sdm', 'id_jadwal', 'tanggal_mulai', 'tanggal_selesai', 'dibuat_oleh']);
            if (empty($data['dibuat_oleh'])) {
                $data['dibuat_oleh'] = $row->dibuat_oleh ?? $this->defaultDibuatOleh();
            }
            $this->service->update($row, $data);
            return $this->response->successResponse('Jadwal karyawan berhasil diupdate');
        });
    }

    /**
     * pesonine.sql: dibuat_oleh BIGINT UNSIGNED NOT NULL.
     * Project ini memakai guard 'admin' dengan PK id_admin (string).
     * Supaya insert tidak gagal, kita isi default numeric (kalau id_admin bukan angka → 0).
     */
    private function defaultDibuatOleh(): int
    {
        $id = optional(auth()->guard('admin')->user())->id_admin;
        $idStr = is_null($id) ? '' : (string) $id;
        return ctype_digit($idStr) ? (int) $idStr : 0;
    }

    public function destroy(string $id): JsonResponse
    {
        $row = $this->service->find((int) $id);
        if (!$row) return $this->response->errorResponse('Data tidak ditemukan', 404);

        return $this->transaction->handleWithTransactionOn('mysql', function () use ($row) {
            $this->service->delete($row);
            return $this->response->successResponse('Jadwal karyawan berhasil dihapus');
        });
    }
}
