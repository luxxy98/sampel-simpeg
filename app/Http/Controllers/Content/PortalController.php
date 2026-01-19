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

final class PortalController extends Controller
{
    public function __construct(
        private readonly ResponseService   $responseService,
        private readonly FileUploadService $fileUploadService
    ) {}

    public function login(): View
    {
        if (Auth::guard('admin')->check()) {
            return view('admin.dashboard');
        }

        return view('portal');
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
}
