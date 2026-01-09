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
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

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
        // SDM: ambil nama dari tabel person
    $sdmOptions = DB::connection('mysql')->table('person_sdm as ps')
        ->leftJoin('person as p', 'p.id_person', '=', 'ps.id_person')
        ->select([
            'ps.id_sdm',
            DB::raw('COALESCE(p.nama, ps.id_sdm) as nama') // fallback aman
        ])
        ->orderBy('nama')
        ->get()
        ->map(fn($r) => [
            'id_sdm' => (int) $r->id_sdm,
            'nama'   => (string) $r->nama,
        ])
        ->toArray();

    // Master Jadwal Kerja
    $jadwalKerjaOptions = DB::connection('mysql')->table('master_jadwal_kerja')
        ->select(['id_jadwal', 'nama_jadwal', 'jam_masuk', 'jam_pulang'])
        ->orderBy('nama_jadwal')
        ->get()
        ->map(fn($r) => [
            'id_jadwal'   => (int) $r->id_jadwal,
            'nama_jadwal' => (string) $r->nama_jadwal,
            'jam_masuk'   => (string) $r->jam_masuk,
            'jam_pulang'  => (string) $r->jam_pulang,
        ])
        ->toArray();

    return view('admin.absensi.jadwal_karyawan.index', compact('sdmOptions', 'jadwalKerjaOptions'));
    }

    public function list(Request $request)
{
    return $this->transaction->handleWithDataTable(
        fn () => $this->service->getListQuery(),
        [
            // kolom datatable: sdm
            'sdm' => fn ($r) => e($r->nama_sdm),

            // kolom datatable: jadwal
            'jadwal' => function ($r) {
                $range = trim(($r->jam_masuk ?? '-') . ' - ' . ($r->jam_pulang ?? '-'));
                return e($r->nama_jadwal) . ' (' . e($range) . ')';
            },

            // kolom datatable: action
            'action' => function ($r) {
                $payload = htmlspecialchars(json_encode([
                    'id_jadwal_karyawan' => (int) $r->id_jadwal_karyawan,
                    'id_sdm' => (int) $r->id_sdm,
                    'id_jadwal' => (int) $r->id_jadwal,
                    'tanggal_mulai' => (string) $r->tanggal_mulai,
                    'tanggal_selesai' => (string) $r->tanggal_selesai,
                ]), ENT_QUOTES, 'UTF-8');

                $id = (int) $r->id_jadwal_karyawan;

                return "
                    <div class='d-flex gap-1 ps-3'>
                        <button type='button' class='btn btn-icon btn-bg-light btn-active-text-primary btn-sm'
                            title='Edit' onclick='openEditJadwalKaryawan({$payload})'>
                            <i class='bi bi-pencil fs-4'></i>
                        </button>
                        <button type='button' class='btn btn-icon btn-bg-light btn-active-text-danger btn-sm'
                            title='Hapus' onclick='deleteJadwalKaryawan({$id})'>
                            <i class='bi bi-trash fs-4'></i>
                        </button>
                    </div>
                ";
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
