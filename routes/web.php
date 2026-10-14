<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\SuratController as AdminSuratController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. Rute Publik (Tanpa Autentikasi)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/pengumuman', [PublicController::class, 'pengumumanIndex'])->name('pengumuman.index');
Route::get('/pengumuman/{id}', [PublicController::class, 'pengumumanShow'])->name('pengumuman.show');

/*
|--------------------------------------------------------------------------
| 2. Rute Autentikasi (Laravel Breeze — dimuat dari routes/auth.php)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| 3. Area Warga (Portal Warga RT 04) — Auth + Role Warga
|--------------------------------------------------------------------------
*/
Route::prefix('warga')->name('warga.')->middleware(['auth', 'warga'])->group(function () {
    Route::view('/', 'warga.beranda')->name('beranda');
    
    Route::view('/profil', 'warga.profil')->name('profil');

    // Data Keluarga & Anggota
    Route::view('/keluarga', 'warga.keluarga')->name('keluarga');

    Route::view('/keluarga/tambah', 'warga.keluarga-tambah')->name('keluarga.tambah');

    Route::post('/keluarga/tambah', function () {
        return redirect()->route('warga.keluarga')
            ->with('success', 'Pendaftaran anggota keluarga baru berhasil diajukan dan menunggu verifikasi Pengurus RT 04.');
    })->name('keluarga.tambah.store');

    Route::get('/keluarga/edit/{nik?}', function ($nik = null) {
        return view('warga.keluarga-edit', compact('nik'));
    })->name('keluarga.edit');

    Route::post('/keluarga/edit', function () {
        return redirect()->route('warga.keluarga')
            ->with('success', 'Pengajuan perubahan data warga berhasil dikirim dan masuk dalam antrean verifikasi RT.');
    })->name('keluarga.edit.store');

    // Layanan Surat Pengantar
    Route::view('/surat', 'warga.surat')->name('surat');

    Route::post('/surat', function () {
        return redirect()->route('warga.surat')
            ->with('success', 'Pengajuan surat pengantar berhasil dikirim dan masuk dalam antrean verifikasi Pengurus RT 04.');
    })->name('surat.store');

    Route::get('/surat/cetak/{id?}', function ($id = 1) {
        return view('warga.surat-cetak', compact('id'));
    })->name('surat.cetak');

    // Rekap Iuran
    Route::view('/iuran', 'warga.iuran')->name('iuran');
});

/*
|--------------------------------------------------------------------------
| 4. Area Admin RT (Portal Pengurus RT 04) — Auth + Role Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('beranda');
    Route::view('/pengaturan', 'admin.pengaturan')->name('pengaturan');

    // Kelola Pengumuman
    Route::get('/pengumuman', [PengumumanController::class, 'TampilkanPengumuman'])->name('pengumuman');
    Route::get('/pengumuman/tambah', [PengumumanController::class, 'CreatePengumuman'])->name('pengumuman.create');
    Route::post('/pengumuman/tambah', [PengumumanController::class, 'simpanPengumuman'])->name('pengumuman.store');
    Route::get('/pengumuman/edit/{id}', [PengumumanController::class, 'editPengumuman'])->name('pengumuman.edit');
    Route::post('/pengumuman/edit/{id}', [PengumumanController::class, 'updatePengumuman'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'hapusPengumuman'])->name('pengumuman.destroy');

    // Kelola Data Warga & Kartu Keluarga
    Route::view('/data-warga', 'admin.data-warga')->name('data-warga');
    Route::view('/data-warga/tambah-anggota', 'admin.warga-tambah-anggota')->name('data-warga.tambah-anggota');
    Route::post('/data-warga/tambah-anggota', function () {
        $nama = request('nama') ?? 'Anggota Baru';
        return redirect()->route('admin.data-warga', ['view' => 'detail'])
            ->with('success', "Anggota keluarga baru ($nama) berhasil ditambahkan ke database RT.");
    })->name('data-warga.tambah-anggota.store');

    Route::get('/data-warga/edit-anggota/{nik?}', function ($nik = '3275015509010003') {
        return view('admin.warga-edit-anggota', compact('nik'));
    })->name('data-warga.edit-anggota');
    Route::post('/data-warga/edit-anggota', function () {
        $nama = request('nama') ?? 'Data Warga';
        return redirect()->route('admin.data-warga', ['view' => 'detail'])
            ->with('success', "Pembaruan data master warga ($nama) berhasil disimpan.");
    })->name('data-warga.edit-anggota.store');

    // Kelola & Verifikasi Pengajuan Surat (UC-12, F013, BR-05, BR-07)
    Route::get('/surat', [AdminSuratController::class, 'index'])->name('surat');
    Route::get('/surat/preview/{id}', [AdminSuratController::class, 'preview'])->name('surat.preview');
    Route::post('/surat/{id}/setujui', [AdminSuratController::class, 'setujui'])->name('surat.setujui');
    Route::post('/surat/{id}/tolak', [AdminSuratController::class, 'tolak'])->name('surat.tolak');

    // Kelola & Rekap Iuran Warga
    Route::view('/iuran', 'admin.iuran')->name('iuran');
});
