<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Hanya izinkan user dengan role 'admin'.
     * Jika belum login → redirect ke login.
     * Jika sudah login tapi bukan admin → redirect ke beranda warga.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        if (! Auth::user()->isAdmin()) {
            return redirect()->route('warga.beranda')
                ->with('error', 'Akses ditolak. Halaman ini hanya untuk Admin RT.');
        }

        return $next($request);
    }
}
