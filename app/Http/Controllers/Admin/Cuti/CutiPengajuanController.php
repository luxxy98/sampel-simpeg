<?php

namespace App\Http\Controllers\Admin\Cuti;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cuti\CutiApprovalRequest;
use App\Http\Requests\Cuti\CutiPengajuanRequest;
use App\Services\Cuti\CutiJenisService;
use App\Services\Cuti\CutiPengajuanService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CutiPengajuanController extends Controller
{
    public function __construct(
        private readonly CutiPengajuanService $service,
        private readonly CutiJenisService $jenisService,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        $toPlain = static fn($r) => is_array($r) ? $r : (method_exists($r, 'toArray') ? $r->toArray() : (array) $r);

        $sdmOptions = $this->service->sdmOptions()->map($toPlain)->all();
        $jenisOptions = $this->jenisService->optionsActive()->map($toPlain)->all();

        return view('admin.cuti.pengajuan.index', compact('sdmOptions', 'jenisOptions'));
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn() => $this->service->listQuery(
                $request->get('tanggal_mulai'),
                $request->get('tanggal_selesai'),
                $request->get('status'),
                $request->get('id_sdm') ? (int) $request->get('id_sdm') : null,
            ),
            [
                'action' => function ($row) {
                    $id = $row->id_cuti;

                    $approveBtn = '';
                    if ($row->status === 'diajukan') {
                        $approveBtn = "
                            <button type='button'
                                data-id='$id'
                                title='Approve'
                                data-bs-toggle='modal'
                                data-bs-target='#form_approve'
                                class='btn btn-icon btn-bg-light btn-active-text-success btn-sm m-1'>
                                <span class='bi bi-check2-square' aria-hidden='true'></span>
                            </button>";
                    }

                    // ✅ tombol cetak hanya jika disetujui
                    $printBtn = '';
                    if ($row->status === 'disetujui') {
                        $printBtn = "
                            <a href='" . route('admin.cuti.pengajuan.print', ['id' => $id]) . "'
                            target='_blank'
                            title='Cetak Surat Cuti'
                            class='btn btn-icon btn-bg-light btn-active-text-dark btn-sm m-1'>
                            <span class='bi bi-printer' aria-hidden='true'></span>
                            </a>";
                    }

                    return implode(' ', [
                        $this->transaction->actionButton($id, 'detail'),
                        $this->transaction->actionButton($id, 'edit'),
                        $approveBtn,
                        $printBtn,
                        $this->transaction->actionButton($id, 'delete'),
                    ]);
                },
            ]
        );
    }

    public function store(CutiPengajuanRequest $request): JsonResponse
    {
        return $this->transaction->handleWithTransaction(function () use ($request) {
            $data = $this->service->create($request->only([
                'id_sdm',
                'id_jenis_cuti',
                'tanggal_mulai',
                'tanggal_selesai',
                'alasan',
            ]));

            return $this->response->successResponse('Pengajuan cuti berhasil dibuat', $data, 201);
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

    public function update(CutiPengajuanRequest $request, string $id): JsonResponse
    {
        $cuti = $this->service->findById($id);
        if (!$cuti) return $this->response->errorResponse('Data tidak ditemukan', 404);

        if (in_array($cuti->status, ['disetujui', 'ditolak'], true)) {
            return $this->response->errorResponse('Pengajuan sudah diputuskan, tidak bisa diedit.');
        }

        return $this->transaction->handleWithTransaction(function () use ($request, $cuti) {
            $updated = $this->service->update($cuti, $request->only([
                'id_sdm',
                'id_jenis_cuti',
                'tanggal_mulai',
                'tanggal_selesai',
                'alasan',
            ]));

            return $this->response->successResponse('Pengajuan cuti berhasil diperbarui', $updated);
        });
    }

    public function approve(CutiApprovalRequest $request, string $id): JsonResponse
    {
        $cuti = $this->service->findById($id);
        if (!$cuti) return $this->response->errorResponse('Data tidak ditemukan', 404);

        if ($cuti->status !== 'diajukan') {
            return $this->response->errorResponse('Pengajuan sudah diproses sebelumnya.');
        }

        return $this->transaction->handleWithTransaction(function () use ($request, $cuti) {
            $adminId = (string) (auth('admin')->user()->id_admin ?? '');
            $updated = $this->service->approve($cuti, $request->status, $request->catatan, $adminId);

            return $this->response->successResponse('Status cuti berhasil diperbarui', $updated);
        });
    }

    public function destroy(string $id): JsonResponse
    {
        $cuti = $this->service->findById($id);
        if (!$cuti) return $this->response->errorResponse('Data tidak ditemukan', 404);

        if ($cuti->status === 'disetujui') {
            return $this->response->errorResponse('Cuti yang sudah disetujui tidak boleh dihapus.');
        }

        return $this->transaction->handleWithTransaction(function () use ($cuti) {
            $this->service->delete($cuti);
            return $this->response->successResponse('Pengajuan cuti berhasil dihapus');
        });
    }

    public function print(string $id): View
    {
        $data = $this->service->getDetailData($id);
        abort_if(!$data, 404);

        // ✅ hanya boleh cetak jika sudah disetujui
        abort_if(($data->status ?? '') !== 'disetujui', 403);

        return view('admin.cuti.pengajuan.print', compact('data'));
    }
}
