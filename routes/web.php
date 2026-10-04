<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/pengumuman', [PublicController::class, 'pengumumanIndex'])->name('pengumuman.index');
Route::get('/pengumuman/{id}', [PublicController::class, 'pengumumanShow'])->name('pengumuman.show');

/*
|--------------------------------------------------------------------------
| Placeholder Auth (Tahap Frontend — dummy redirect berdasarkan username)
| Akan diganti pada Tahap Backend dengan controller Breeze yang sudah
| disesuaikan (login pakai username/NIK, registrasi alur UC-01).
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('public.login');
})->name('login');

Route::get('/daftar', function () {
    return view('public.register');
})->name('register');

Route::post('/daftar', function () {
    return redirect()->route('login')->with('success', 'Pendaftaran berhasil dikirim. Menunggu verifikasi Admin RT.');
})->name('register.post');

/*
|--------------------------------------------------------------------------
| Dummy POST Login — navigasi sementara lintas peran (F. Frontend Fase F0)
| Dihapus pada Tahap Backend.
|--------------------------------------------------------------------------
*/
Route::post('/login', function () {
    $username = request('username') ?? request('nik_atau_username');
    if ($username === 'adminrt' || $username === 'admin') {
        return redirect()->route('admin.beranda');
    }
    return redirect()->route('warga.beranda');
})->name('login.post');

/*
|--------------------------------------------------------------------------
| Area Warga (placeholder — akan diisi F2)
|--------------------------------------------------------------------------
*/
Route::prefix('warga')->name('warga.')->group(function () {
    Route::get('/', function () { return view('warga.beranda'); })->name('beranda');
    Route::get('/keluarga', function () { return view('warga.keluarga'); })->name('keluarga');
    Route::get('/surat', function () { return view('warga.surat'); })->name('surat');
    Route::get('/iuran', function () { return view('warga.iuran'); })->name('iuran');
    Route::get('/profil', function () { return view('warga.profil'); })->name('profil');
});

/*
|--------------------------------------------------------------------------
| Area Admin RT (placeholder — akan diisi F3)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () { return view('admin.beranda'); })->name('beranda');

    // Kelola Pengumuman
    Route::get('/pengumuman', function () { return view('admin.pengumuman'); })->name('pengumuman');
    Route::get('/pengumuman/tambah', function () { return view('admin.pengumuman-form'); })->name('pengumuman.create');
    Route::post('/pengumuman/tambah', function () {
        $status = request('status') ?? request('status_publikasi') ?? 'draft';
        return redirect()->route('admin.pengumuman')
            ->with('success', 'Pengumuman berhasil ' . ($status === 'publish' ? 'dipublikasikan' : 'disimpan sebagai draf') . '.');
    })->name('pengumuman.store');

    // Kelola Data Warga & Anggota Keluarga
    Route::get('/data-warga', function () { return view('admin.data-warga'); })->name('data-warga');
    Route::get('/data-warga/tambah-anggota', function () { return view('admin.warga-tambah-anggota'); })->name('data-warga.tambah-anggota');
    Route::post('/data-warga/tambah-anggota', function () {
        $nama = request('nama') ?? 'Anggota Baru';
        return redirect()->route('admin.data-warga', ['view' => 'detail'])
            ->with('success', 'Anggota keluarga baru (' . $nama . ') berhasil ditambahkan ke database RT.');
    })->name('data-warga.tambah-anggota.store');

    Route::get('/data-warga/edit-anggota/{nik?}', function ($nik = '3275015509010003') {
        return view('admin.warga-edit-anggota', compact('nik'));
    })->name('data-warga.edit-anggota');
    Route::post('/data-warga/edit-anggota', function () {
        $nama = request('nama') ?? 'Data Warga';
        return redirect()->route('admin.data-warga', ['view' => 'detail'])
            ->with('success', 'Pembaruan data master warga (' . $nama . ') berhasil disimpan.');
    })->name('data-warga.edit-anggota.store');
    Route::get('/surat', function () { return view('admin.surat'); })->name('surat');
    Route::get('/surat/preview/{id?}', function ($id = 1) {
        $surat = \App\Data\DummyData::findPengajuanSuratAdmin($id);
        return view('admin.surat-preview', compact('surat', 'id'));
    })->name('surat.preview');
    Route::get('/iuran', function () { return view('admin.iuran'); })->name('iuran');
    Route::get('/pengaturan', function () { return view('admin.pengaturan'); })->name('pengaturan');
});
