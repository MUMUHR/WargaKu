<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman Profil & Keamanan Akun Admin RT
     */
    public function index(): View
    {
        $admin = auth()->user();

        return view('admin.pengaturan', compact('admin'));
    }

    /**
     * Memperbarui nomor kontak / telepon WhatsApp Admin RT
     */
    public function updateKontak(Request $request): RedirectResponse
    {
        $request->validate([
            'no_telp' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
        ], [
            'no_telp.required' => 'Nomor telepon / WhatsApp pengurus wajib diisi.',
            'no_telp.regex'    => 'Format nomor telepon tidak valid. Gunakan angka atau tanda + / -.',
        ]);

        $admin = auth()->user();
        $admin->update([
            'no_telp' => $request->input('no_telp'),
        ]);

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Nomor telepon / WhatsApp pengurus RT berhasil diperbarui.');
    }

    /**
     * Memperbarui kata sandi akun Admin RT
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
            'konfirmasi_password.same'     => 'Konfirmasi kata sandi baru tidak sesuai. Silakan periksa kembali.',
        ]);

        $admin = auth()->user();

        if (!Hash::check($request->input('password_saat_ini'), $admin->password)) {
            return redirect()->route('admin.pengaturan')
                ->withErrors(['password_saat_ini' => 'Kata sandi saat ini yang Anda masukkan salah.'])
                ->withInput();
        }

        $admin->update([
            'password' => Hash::make($request->input('password_baru')),
        ]);

        return redirect()->route('admin.pengaturan')
            ->with('success', 'Password akun Admin RT berhasil diperbarui! Silakan catat kata sandi baru Anda.');
    }
}
