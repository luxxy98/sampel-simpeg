<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Services\Tools\FileUploadService;
use App\Services\Tools\ResponseService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Absensi\Absensi;
use App\Models\App\Audit;
use App\Models\Cuti\CutiPengajuan;
use App\Models\Gaji\GajiPeriode;
use App\Models\Gaji\GajiTrx;
use App\Models\Person\Person;
use App\Models\Sdm\PersonSdm;
use App\Models\Sppd\Sppd;
use Illuminate\Support\Facades\Schema;

final class PortalController extends Controller
{
    public function __construct(
        private readonly ResponseService   $responseService,
        private readonly FileUploadService $fileUploadService
    ) {}

    public function login(): View
    {
        if (!Auth::guard('admin')->check()) {
            return view('portal');
        }

        $admin = Auth::guard('admin')->user();
        $today = Carbon::today();
        

        // === KPI (aman walau tabel belum ada) ===
        $personTotal = $this->tableExists('mysql', 'person') ? Person::count() : 0;
        $sdmTotal = $this->tableExists('mysql', 'person_sdm') ? PersonSdm::count() : 0;

        $absensiToday = $this->tableExists('mysql', 'absensi')
            ? Absensi::whereDate('tanggal', $today->format('Y-m-d'))->count()
            : 0;

        $cutiPending = $this->tableExists('mysql', 'cuti_pengajuan')
            ? CutiPengajuan::where('status', 'diajukan')->count()
            : 0;

        $sppdPending = $this->tableExists('mysql', 'sppd')
            ? Sppd::where('status', 'diajukan')->count()
            : 0;

        // === Chart Absensi 14 hari terakhir (line/area) ===
        $absensiLabels = [];
        $absensiSeries = [];

        if ($this->tableExists('mysql', 'absensi')) {
            $days = 14;
            $start = $today->copy()->subDays($days - 1);

            $map = Absensi::query()
                ->selectRaw('DATE(tanggal) as t, COUNT(*) as total')
                ->whereBetween('tanggal', [$start->format('Y-m-d'), $today->format('Y-m-d')])
                ->groupBy('t')
                ->pluck('total', 't');

            for ($i = 0; $i < $days; $i++) {
                $d = $start->copy()->addDays($i)->format('Y-m-d');
                $absensiLabels[] = Carbon::parse($d)->translatedFormat('d M');
                $absensiSeries[] = (int)($map[$d] ?? 0);
            }
        }

        // === Chart Distribusi Status Cuti (donut) ===
        $cutiLabels = [];
        $cutiSeries = [];

        if ($this->tableExists('mysql', 'cuti_pengajuan')) {
            $statusMap = CutiPengajuan::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            foreach ($statusMap as $status => $total) {
                $cutiLabels[] = mb_strtoupper((string)$status);
                $cutiSeries[] = (int)$total;
            }
        }

        // === Ringkas Gaji (periode terakhir) ===
        $gajiPeriodeLabel = '-';
        $gajiTrxCount = 0;
        $gajiThpSum = 0;

        if ($this->tableExists('absensigaji', 'gaji_periode') && $this->tableExists('absensigaji', 'gaji_trx')) {
            $periode = GajiPeriode::query()->orderByDesc('id_periode')->first();
            if ($periode) {
                $gajiPeriodeLabel = sprintf('%02d/%d', (int)$periode->bulan, (int)$periode->tahun);

                $q = GajiTrx::query()->where('id_periode', (int)$periode->id_periode);
                $gajiTrxCount = (int)$q->count();
                $gajiThpSum = (int) round((float)$q->sum('total_take_home_pay'));
            }
        }

        // === Aktivitas terakhir (opsional: audits) ===
        $recentAudits = collect();
        if ($this->tableExists('mysql', 'audits')) {
            $recentAudits = Audit::query()
                ->orderByDesc('created_at')
                ->limit(8)
                ->get(['event', 'auditable_type', 'auditable_id', 'user_id', 'created_at']);
        }

        return view('admin.dashboard', compact(
            'admin',
            'personTotal',
            'sdmTotal',
            'absensiToday',
            'cutiPending',
            'sppdPending',
            'absensiLabels',
            'absensiSeries',
            'cutiLabels',
            'cutiSeries',
            'gajiPeriodeLabel',
            'gajiTrxCount',
            'gajiThpSum',
            'recentAudits',
        ));
    }

    public function logindb(Request $request): RedirectResponse
    {
        $username = $request->input('username');
        $password = $request->input('password');
        $recaptchaResponse = $request->input('g-recaptcha-response');

        $validationRules = [
            'username' => 'required',
            'password' => 'required',
        ];
        
        // Only require reCAPTCHA in production
        if (config('app.env') === 'production') {
            $validationRules['g-recaptcha-response'] = 'required';
        }

        $customMessages = [
            'username.required' => 'Nama Pengguna harus diisi.',
            'password.required' => 'Kata Kunci harus diisi.',
            'g-recaptcha-response.required' => 'Harap verifikasi bahwa Anda bukan robot.',
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validasi reCAPTCHA di production sebelum login attempt
        if (config('app.env') === 'production') {
            if (!$this->validateRecaptcha($recaptchaResponse)) {
                return redirect()
                    ->back()
                    ->withErrors(['g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        if (Auth::guard('admin')->attempt(['email' => $username, 'password' => $password])) {
            return redirect()->intended();
        }

        return redirect()->back()->with('error', 'nama pengguna dan kata kunci salah')->withInput();
    }

    /**
     * Validate reCAPTCHA token
     */
    private function validateRecaptcha(string $recaptchaResponse): bool
    {
        $secretKey = config('services.recaptcha.secret_key');

        if (empty($secretKey)) {
            Log::warning('reCAPTCHA secret key tidak ditemukan');
            return false;
        }

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => request()->ip()
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        try {
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $response = json_decode($result);

            return $response->success ?? false;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA validation error: ' . $e->getMessage());
            return false;
        }
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();

        return redirect()->route('index')->with('success', 'Anda telah berhasil keluar.');
    }

    public function error(Request $request): JsonResponse
    {
        $csrfToken = $request->header('X-CSRF-TOKEN');

        if ($csrfToken !== csrf_token()) {
            return $this->responseService->errorResponse('Token CSRF tidak valid.');
        }

        Log::channel('daily')->error('client-error', ['data' => $request->all()]);

        return $this->responseService->successResponse('Error berhasil dicatat.');
    }

    public function viewFile(Request $request, string $dir, string $filename): BinaryFileResponse|StreamedResponse
    {
        return $this->fileUploadService->viewFile($request, $dir, $filename);
    }

    private function tableExists(string $connection, string $table): bool
    {
        try {
            return Schema::connection($connection)->hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
