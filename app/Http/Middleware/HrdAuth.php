<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Login sederhana untuk tim HR (satu akun, diatur lewat .env). */
class HrdAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $password = (string) config('hrd.auth.password');

        // Password belum diatur: boleh di lokal, DIBLOKIR di production (supaya data tidak terbuka).
        if ($password === '') {
            abort_if(app()->isProduction(), 503, 'HRD_PASSWORD belum diatur di server. Isi dulu variabel HRD_PASSWORD.');

            return $next($request);
        }

        if ($request->session()->get('hrd_auth') === true) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Sesi berakhir, silakan login ulang.'], 401);
        }

        return redirect()->route('login');
    }
}
