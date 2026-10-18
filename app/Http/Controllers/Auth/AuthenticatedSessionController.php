<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view (menggunakan view kustom WargaKu).
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.beranda')
                : redirect()->route('warga.beranda');
        }

        return view('public.login');
    }

    /**
     * Handle an incoming authentication request.
     * Redirect berdasarkan role: admin → /admin, warga → /warga
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        $nama = $user->wargaPemilik?->nama 
            ?? $user->wargas?->first()?->nama 
            ?? $user->username;

        if ($user->isAdmin()) {
            return redirect()->route('admin.beranda')
                ->with('success', 'Selamat datang kembali, Administrator RT 04!');
        }

        return redirect()->route('warga.beranda')
            ->with('success', "Selamat datang kembali, {$nama}! Anda berhasil masuk.");
    }

    /**
     * Destroy an authenticated session (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil keluar dari sistem WargaKu.');
    }
}
