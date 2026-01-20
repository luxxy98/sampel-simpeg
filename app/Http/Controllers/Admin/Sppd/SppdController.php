<?php

namespace App\Http\Controllers\Admin\Sppd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sppd\SppdApprovalRequest;
use App\Http\Requests\Sppd\SppdRequest;
use App\Services\Sppd\SppdService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SppdController extends Controller
{
    public function __construct(
        private readonly SppdService $service,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        $toPlain = static fn($r) => is_array($r) ? $r : (method_exists($r, 'toArray') ? $r->toArray() : (array) $r);
        $sdmOptions = $this->service->sdmOptions()->map($toPlain)->all();

        return view('admin.sppd.index', compact('sdmOptions'));
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn() => $this->service->listQuery(
                $request->get('status'),
                $request->get('id_sdm') ? (int) $request->get('id_sdm') : null,
            ),
            [
                'action' => function ($row) {
    $id = (int) data_get($row, 'id_sppd');

    // normalisasi status biar aman (menghindari spasi / beda case)
    $status = strtolower(trim((string) data_get($row, 'status', '')));

    $approveBtn = '';
    $submitBtn = '';
    $selesaiBtn = '';

    if ($status === 'draft') {
        $submitBtn = "
            <button type='button'
                data-id='{$id}'
                title='Ajukan'
                data-bs-toggle='modal'
                data-bs-target='#form_submit'
                class='btn btn-icon btn-bg-light btn-active-text-warning btn-sm m-1'>
                <span class='bi bi-box-arrow-up-right' aria-hidden='true'></span>
            </button>";
    }

    if ($status === 'draft' || $status === 'diajukan') {
        $approveBtn = "
            <button type='button'
                data-id='{$id}'
                title='Approve'
                data-bs-toggle='modal'
                data-bs-target='#form_approve'
                class='btn btn-icon btn-bg-light btn-active-text-success btn-sm m-1'>
                <span class='bi bi-check2-square' aria-hidden='true'></span>
            </button>";
    }

    if ($status === 'disetujui') {
        $selesaiBtn = "
            <button type='button'
                onclick='selesaiConfirmation({$id})'
                title='Selesai'
                class='btn btn-icon btn-bg-light btn-active-text-success btn-sm m-1'>
                <span class='bi bi-flag' aria-hidden='true'></span>
            </button>";
    }

    // Tombol cetak hanya muncul ketika SPPD sudah di-approve (status: disetujui/selesai)
    $printBtn = '';
    if (in_array($status, ['disetujui', 'selesai'], true)) {
        $printBtn = "
            <a href='" . route('admin.sppd.print', ['id' => $id]) . "'
            target='_blank'
            title='Cetak'
            class='btn btn-icon btn-bg-light btn-active-text-dark btn-sm m-1'>
            <span class='bi bi-printer' aria-hidden='true'></span>
            </a>";
    }

    return implode(' ', [
        $this->transaction->actionButton($id, 'detail'),
        $this->transaction->actionButton($id, 'edit'),
        $submitBtn,
        $approveBtn,
        $selesaiBtn,
        $printBtn,
        $this->transaction->actionButton($id, 'delete'),
    ]);
},

            ]
        );
    }

    public function store(SppdRequest $request): JsonResponse
    {
        return $this->transaction->handleWithTransaction(function () use ($request) {
            $adminId = (string) (auth('admin')->user()->id_admin ?? '');
            $data = $this->service->create($request->only([
                'id_sdm','nomor_surat','tanggal_surat','tanggal_berangkat','tanggal_pulang',
                'tujuan','instansi_tujuan','maksud_tugas','transportasi',
                'biaya_transport','biaya_penginapan','uang_harian','biaya_lainnya',
            ]), $adminId);

            return $this->response->successResponse('SPPD berhasil dibuat', $data, 201);
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

    public function update(SppdRequest $request, string $id): JsonResponse
    {
        $sppd = $this->service->findById($id);
        if (!$sppd) return $this->response->errorResponse('Data tidak ditemukan', 404);

        if (in_array($sppd->status, ['disetujui','selesai'], true)) {
            return $this->response->errorResponse('SPPD sudah disetujui/selesai, tidak bisa diedit.');
        }

        return $this->transaction->handleWithTransaction(function () use ($request, $sppd) {
            $updated = $this->service->update($sppd, $request->only([
                'id_sdm','nomor_surat','tanggal_surat','tanggal_berangkat','tanggal_pulang',
                'tujuan','instansi_tujuan','maksud_tugas','transportasi',
                'biaya_transport','biaya_penginapan','uang_harian','biaya_lainnya',
            ]));

            return $this->response->successResponse('SPPD berhasil diperbarui', $updated);
        });
    }

    public function submit(string $id): JsonResponse
    {
        $sppd = $this->service->findById($id);
        if (!$sppd) return $this->response->errorResponse('Data tidak ditemukan', 404);
        if ($sppd->status !== 'draft') return $this->response->errorResponse('Hanya status draft yang bisa diajukan.');

        return $this->transaction->handleWithTransaction(function () use ($sppd) {
            $updated = $this->service->submit($sppd);
            return $this->response->successResponse('SPPD berhasil diajukan', $updated);
        });
    }

    public function approve(SppdApprovalRequest $request, string $id): JsonResponse
    {
        $sppd = $this->service->findById($id);
        if (!$sppd) return $this->response->errorResponse('Data tidak ditemukan', 404);
        if (!in_array($sppd->status, ['draft', 'diajukan'], true)) {
            return $this->response->errorResponse('Hanya status draft atau diajukan yang bisa diproses.');
        }

        return $this->transaction->handleWithTransaction(function () use ($request, $sppd) {
            $adminId = (string) (auth('admin')->user()->id_admin ?? '');
            $updated = $this->service->approve($sppd, $request->status, $request->catatan, $adminId);
            return $this->response->successResponse('Status SPPD berhasil diperbarui', $updated);
        });
    }

    public function selesai(string $id): JsonResponse
    {
        $sppd = $this->service->findById($id);
        if (!$sppd) return $this->response->errorResponse('Data tidak ditemukan', 404);
        if ($sppd->status !== 'disetujui') return $this->response->errorResponse('Hanya status disetujui yang bisa diselesaikan.');

        return $this->transaction->handleWithTransaction(function () use ($sppd) {
            $updated = $this->service->selesai($sppd);
            return $this->response->successResponse('SPPD ditandai selesai', $updated);
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $sppd = $this->service->findById($id);
        if (!$sppd) return $this->response->errorResponse('Data tidak ditemukan', 404);

        if ($sppd->status === 'disetujui' || $sppd->status === 'selesai') {
            return $this->response->errorResponse('SPPD disetujui/selesai tidak boleh dihapus.');
        }

        return $this->transaction->handleWithTransaction(function () use ($sppd) {
            $this->service->delete($sppd);
            return $this->response->successResponse('SPPD berhasil dihapus');
        });
    }

    public function print(string $id): View
    {
        $data = $this->service->getDetailData($id);
        abort_if(!$data, 404);

        return view('admin.sppd.print', compact('data'));
    }
}
