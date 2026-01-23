<?php

namespace App\Http\Controllers\Admin\Log;

use App\Http\Controllers\Controller;
use App\Services\Log\DbQueryLogService;
use App\Services\Tools\TransactionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

final class DbQueryLogController extends Controller
{
    public function __construct(
        private readonly DbQueryLogService $dbQueryLogService,
        private readonly TransactionService $transactionService,
    ) {}

    public function index(): View
    {
        return view('admin.log.database.index');
    }

    public function list(): JsonResponse
    {
        return $this->transactionService->handleWithDataTable(
            fn() => $this->dbQueryLogService->getListData(),
            [
                'action' => fn($row) => $this->transactionService->actionButton((string)$row->id, 'detail'),
            ]
        );
    }

    public function show(int $id): JsonResponse
    {
        $data = $this->dbQueryLogService->findById($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($data);
    }
}
