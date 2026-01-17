<?php

namespace App\Http\Controllers\Admin\Cuti;

use App\Http\Controllers\Controller;
use App\Services\Cuti\CutiJenisService;
use App\Services\Cuti\CutiRekapService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CutiRekapController extends Controller
{
    public function __construct(
        private readonly CutiRekapService $service,
        private readonly CutiJenisService $jenisService,
        private readonly TransactionService $transaction,
        private readonly ResponseService $response,
    ) {}

    public function index(): View
    {
        $toPlain = static fn($r) => is_array($r) ? $r : (method_exists($r, 'toArray') ? $r->toArray() : (array) $r);

        $sdmOptions = $this->service->sdmOptions()->map($toPlain)->all();
        $jenisOptions = $this->jenisService->optionsActive()->map($toPlain)->all();

        return view('admin.cuti.rekap.index', compact('sdmOptions', 'jenisOptions'));
    }

    public function list(Request $request): JsonResponse
    {
        return $this->transaction->handleWithDataTable(
            fn() => $this->service->rekapQuery(
                $request->get('status'),
                $request->get('id_sdm') ? (int) $request->get('id_sdm') : null,
                $request->get('id_jenis_cuti') ? (int) $request->get('id_jenis_cuti') : null,
            ),
            []
        );
    }
}
