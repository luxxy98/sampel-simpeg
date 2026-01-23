<?php

namespace App\Providers;

use App\Services\Tools\FileUploadService;
use App\Services\Tools\ResponseService;
use App\Services\Tools\SyncFingerspotService;
use App\Services\Tools\TransactionService;
use App\Services\Tools\ValidationService;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\Log\DbQueryLog;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $enforceHttps = $this->app->environment('production');

        // =========================
        // DB Query Logger (UAS)
        // Aktifkan via .env: DB_QUERY_LOG_ENABLED=true
        // =========================
        if (env('DB_QUERY_LOG_ENABLED', false) && !app()->runningInConsole()) {

            DB::listen(function (QueryExecuted $query) {

                // ✅ Filter: hanya log request admin
                if (!request() || !str_starts_with(request()->path(), 'admin')) {
                    return;
                }

                // Hindari loop: jangan log query yang terjadi di connection 'log'
                if ($query->connectionName === 'log') {
                    return;
                }

                // Hindari self-log kalau suatu saat query ini nyangkut
                $sqlLower = strtolower($query->sql);
                if (str_contains($sqlLower, 'db_query_logs')) {
                    return;
                }

                // Info request (kalau ada)
                $url = null;
                $routeName = null;
                $method = null;
                $ip = null;
                $ua = null;

                if (request()) {
                    $url = request()->fullUrl();
                    $routeName = optional(request()->route())->getName();
                    $method = request()->method();
                    $ip = request()->ip();
                    $ua = request()->userAgent();
                }

                // Info admin (kalau sedang login)
                $adminId = null;
                $adminName = null;
                try {
                    $admin = Auth::guard('admin')->user();
                    if ($admin) {
                        $adminId = (string) $admin->getAuthIdentifier();
                        $adminName = $admin->nama ?? null;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }

                DbQueryLog::create([
                    'connection_name' => $query->connectionName,
                    'sql_text' => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms' => $query->time,
                    'url' => $url,
                    'route_name' => $routeName,
                    'method' => $method,
                    'user_id' => $adminId,
                    'user_name' => $adminName,
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                    'created_at' => now(),
                ]);
            });
        }


        URL::forceHttps($enforceHttps);

        if ($enforceHttps) {
            $this->app->make('request')->server->set('HTTPS', 'on');
        }
    }

    public function register(): void
    {
        $this->app->singleton(ResponseService::class);
        $this->app->singleton(ValidationService::class);

        $this->app->singleton(TransactionService::class, static fn($app): TransactionService => new TransactionService(
            responseService: $app->make(ResponseService::class),
        ));


        $this->app->singleton(FileUploadService::class);

    }
}
