<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * Middleware untuk memastikan user memiliki role yang diizinkan.
     * Penggunaan di routes:
     *   ->middleware('role:CPM')           // hanya CPM
     *   ->middleware('role:CPM,SPV')       // CPM atau SPV
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role;

        if (!in_array($userRole, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akses ditolak. Role Anda tidak memiliki izin untuk tindakan ini.'], 403);
            }

            return redirect()->back()->with('error', 'Akses ditolak. Hanya ' . implode(' atau ', $roles) . ' yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
