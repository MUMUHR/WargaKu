<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman Profil & Keamanan Akun Warga
     */
    public function index(): View
    {
        $user = auth()->user();
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        return view('warga.profil', compact('user', 'kepala', 'keluarga'));
    }

    /**
     * Memperbarui nomor telepon / WhatsApp akun warga
     */
    public function updateKontak(Request $request): RedirectResponse
    {
        $request->validate([
            'no_telp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
        ], [
            'no_telp.required' => 'Nomor telepon / WhatsApp wajib diisi.',
            'no_telp.regex'    => 'Format nomor telepon tidak valid. Gunakan angka atau tanda + / -.',
        ]);

        $user = auth()->user();
        $user->update([
            'no_telp' => $request->input('no_telp'),
        ]);

        return redirect()->route('warga.profil')
            ->with('success', 'Nomor telepon / WhatsApp berhasil diperbarui!');
    }

    /**
     * Memperbarui kata sandi akun warga
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_saat_ini'   => ['required', 'string'],
            'password_baru'       => ['required', 'string', 'min:5', 'different:password_saat_ini'],
            'konfirmasi_password' => ['required', 'string', 'same:password_baru'],
        ], [
            'password_saat_ini.required'   => 'Password saat ini wajib diisi.',
            'password_baru.required'       => 'Password baru wajib diisi.',
            'password_baru.min'            => 'Password baru minimal harus 5 karakter.',
            'password_baru.different'      => 'Password baru tidak boleh sama dengan password saat ini.',
            'konfirmasi_password.required' => 'Konfirmasi kata sandi baru wajib diisi.',
            'konfirmasi_password.same'     => 'Konfirmasi kata sandi baru tidak cocok dengan kata sandi baru.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->input('password_saat_ini'), $user->password)) {
            return redirect()->route('warga.profil')
                ->withErrors(['password_saat_ini' => 'Kata sandi saat ini yang Anda masukkan salah.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->input('password_baru')),
        ]);

        return redirect()->route('warga.profil')
            ->with('success', 'Kata sandi berhasil diganti! Harap catat kata sandi baru Anda.');
    }
}
