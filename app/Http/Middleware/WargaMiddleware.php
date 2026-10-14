<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WargaMiddleware
{
    /**
     * Handle an incoming request.
     * Hanya izinkan user dengan role 'warga'.
     * Jika belum login → redirect ke login.
     * Jika sudah login tapi bukan warga (misal admin) → redirect ke beranda admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! Auth::user()->isWarga()) {
            return redirect()->route('admin.beranda')
                ->with('info', 'Anda masuk sebagai Admin RT.');
        }

        return $next($request);
    }
}
