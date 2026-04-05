<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AssetFinding;
use App\Models\BeritaAcara;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DevController extends Controller
{
    public function index()
    {
        $health = $this->runHealthChecks();
        $stats  = $this->getAppStats();
        $logs   = $this->getActivityLogs();
        $alerts = $this->getAlerts($health, $stats);

        return view('dev.index', compact('health', 'stats', 'logs', 'alerts'));
    }

    public function refresh()
    {
        $health = $this->runHealthChecks();
        $stats  = $this->getAppStats();
        $alerts = $this->getAlerts($health, $stats);

        return response()->json([
            'health' => $health,
            'stats'  => $stats,
            'alerts' => $alerts,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    private function getActivityLogs()
    {
        try {
            return ActivityLog::with('user')->latest()->take(15)->get();
        } catch (\Exception $e) {
            return collect(); // kembalikan collection kosong jika tabel belum ada
        }
    }

    // ─────────────────────────────────────────────
    //  Health Checks
    // ─────────────────────────────────────────────

    private function runHealthChecks(): array
    {
        return [
            'database' => $this->checkDatabase(),
            'vercel'   => $this->checkVercel(),
            'php'      => $this->checkPhp(),
            'cache'    => $this->checkCache(),
            'queue'    => $this->checkQueue(),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            $start  = microtime(true);
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);

            $tables = DB::select("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ");

            return [
                'status'  => 'online',
                'latency' => $latency,
                'driver'  => config('database.default'),
                'host'    => config('database.connections.' . config('database.default') . '.host'),
                'port'    => config('database.connections.' . config('database.default') . '.port'),
                'tables'  => count($tables),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'offline',
                'error'  => $e->getMessage(),
            ];
        }
    }

    private function checkVercel(): array
    {
        $url = 'https://a-u-r-a.vercel.app/ping';
        try {
            $start = microtime(true);
            $ctx   = stream_context_create(['http' => [
                'method'          => 'HEAD',
                'timeout'         => 8,
                'ignore_errors'   => true,
                'follow_location' => false, // Jangan follow 302
            ]]);
            $response   = @file_get_contents($url, false, $ctx);
            $latency    = round((microtime(true) - $start) * 1000, 2);
            $httpStatus = isset($http_response_header[0])
                ? (int) explode(' ', $http_response_header[0])[1]
                : 0;

            return [
                'status'      => ($httpStatus === 200) ? 'online' : 'warn',
                'http_code'   => $httpStatus,
                'latency'     => $latency,
                'url'         => $url,
            ];
        } catch (\Exception $e) {
            return [
                'status'    => 'offline',
                'http_code' => 0,
                'latency'   => 0,
                'url'       => $url,
                'error'     => $e->getMessage(),
            ];
        }
    }

    private function checkPhp(): array
    {
        return [
            'status'    => 'online',
            'php'       => PHP_VERSION,
            'laravel'   => app()->version(),
            'env'       => config('app.env'),
            'debug'     => config('app.debug') ? 'ON ⚠' : 'OFF ✓',
            'timezone'  => config('app.timezone'),
            'memory'    => round(memory_get_usage(true) / 1024 / 1024, 1) . ' MB',
            'mem_limit' => ini_get('memory_limit'),
        ];
    }

    private function checkCache(): array
    {
        try {
            $key = 'dev_health_check_' . time();
            Cache::put($key, 'ok', 5);
            $val = Cache::get($key);
            Cache::forget($key);

            return [
                'status' => ($val === 'ok') ? 'online' : 'warn',
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'driver' => config('cache.default'), 'error' => $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        try {
            $pending = DB::table('jobs')->count();
            $failed  = DB::table('failed_jobs')->count();

            return [
                'status'  => $failed > 0 ? 'warn' : 'online',
                'driver'  => config('queue.default'),
                'pending' => $pending,
                'failed'  => $failed,
            ];
        } catch (\Exception $e) {
            return ['status' => 'online', 'driver' => config('queue.default'), 'pending' => 0, 'failed' => 0];
        }
    }

    // ─────────────────────────────────────────────
    //  App Stats
    // ─────────────────────────────────────────────

    private function getAppStats(): array
    {
        $activityCount = 0;
        try {
            $activityCount = ActivityLog::count();
        } catch (\Exception $e) {
            // tabel activity_logs belum ada, abaikan
        }

        return [
            'users'              => User::count(),
            'findings_total'     => AssetFinding::count(),
            'findings_pending'   => AssetFinding::where('status', 'Pending Approval')->count(),
            'findings_open'      => AssetFinding::where('status', 'Open')->count(),
            'findings_progress'  => AssetFinding::where('status', 'On Progress')->count(),
            'findings_done'      => AssetFinding::where('status', 'Done')->count(),
            'ba_total'           => BeritaAcara::count(),
            'ba_active'          => BeritaAcara::whereIn('status', ['Submitted', 'Processed'])->count(),
            'ba_done'            => BeritaAcara::where('status', 'Done')->count(),
            'activity_logs'      => $activityCount,
            'roles'              => User::select('role', DB::raw('count(*) as total'))->groupBy('role')->get(),
        ];
    }

    // ─────────────────────────────────────────────
    //  Alert Generator
    // ─────────────────────────────────────────────

    private function getAlerts(array $health, array $stats): array
    {
        $alerts = [];

        if (($health['database']['status'] ?? '') !== 'online') {
            $alerts[] = ['level' => 'critical', 'msg' => 'Koneksi database bermasalah!'];
        }
        if (($health['database']['latency'] ?? 0) > 500) {
            $alerts[] = ['level' => 'warn', 'msg' => 'Latensi database sangat tinggi (' . $health['database']['latency'] . ' ms)'];
        }
        if (($health['vercel']['status'] ?? '') === 'offline') {
            $alerts[] = ['level' => 'critical', 'msg' => 'Server Vercel tidak dapat dijangkau!'];
        }
        if (($health['vercel']['latency'] ?? 0) > 3000) {
            $alerts[] = ['level' => 'warn', 'msg' => 'Response time Vercel lambat (' . $health['vercel']['latency'] . ' ms)'];
        }
        if (($health['queue']['failed'] ?? 0) > 0) {
            $alerts[] = ['level' => 'warn', 'msg' => $health['queue']['failed'] . ' queue job gagal perlu ditangani.'];
        }
        if (config('app.debug') && config('app.env') === 'production') {
            $alerts[] = ['level' => 'critical', 'msg' => 'APP_DEBUG masih aktif di production! Segera matikan di .env server.'];
        }
        if ($stats['findings_pending'] > 5) {
            $alerts[] = ['level' => 'info', 'msg' => $stats['findings_pending'] . ' laporan temuan menumpuk, belum di-approve CPM.'];
        }

        return $alerts;
    }
}
