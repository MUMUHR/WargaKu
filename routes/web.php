<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IuranController as AdminIuranController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\SuratController as AdminSuratController;
use App\Http\Controllers\Admin\WargaController as AdminWargaController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Warga\DashboardController as WargaDashboardController;
use App\Http\Controllers\Warga\IuranController as WargaIuranController;
use App\Http\Controllers\Warga\KeluargaController as WargaKeluargaController;
use App\Http\Controllers\Warga\ProfilController as WargaProfilController;
use App\Http\Controllers\Warga\SuratController as WargaSuratController;
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
    Route::get('/', [WargaDashboardController::class, 'index'])->name('beranda');
    
    // Profil & Keamanan Warga
    Route::get('/profil', [WargaProfilController::class, 'index'])->name('profil');
    Route::post('/profil/kontak', [WargaProfilController::class, 'updateKontak'])->name('profil.kontak');
    Route::post('/profil/password', [WargaProfilController::class, 'updatePassword'])->name('profil.password');

    // Data Keluarga & Anggota
    Route::get('/keluarga', [WargaKeluargaController::class, 'index'])->name('keluarga');

    Route::get('/keluarga/tambah', [WargaKeluargaController::class, 'tampilkanFormTambah'])->name('keluarga.tambah');
    Route::post('/keluarga/tambah', [WargaKeluargaController::class, 'simpanPengajuanTambah'])->name('keluarga.tambah.store');

    Route::get('/keluarga/edit/{nik?}', [WargaKeluargaController::class, 'tampilkanFormEdit'])->name('keluarga.edit');
    Route::post('/keluarga/edit', [WargaKeluargaController::class, 'simpanPengajuanEdit'])->name('keluarga.edit.store');

    // Layanan Surat Pengantar
    Route::get('/surat', [WargaSuratController::class, 'index'])->name('surat');
    Route::post('/surat', [WargaSuratController::class, 'simpan'])->name('surat.store');
    Route::post('/surat/{id}/batal', [WargaSuratController::class, 'batalkan'])->name('surat.batal');
    Route::get('/surat/cetak/{id}', [WargaSuratController::class, 'cetak'])->name('surat.cetak');
    Route::get('/surat/{id}/preview', [WargaSuratController::class, 'cetak'])->name('surat.preview');

    // Rekap Iuran
    Route::get('/iuran', [WargaIuranController::class, 'index'])->name('iuran');
});

/*
|--------------------------------------------------------------------------
| 4. Area Admin RT (Portal Pengurus RT 04) — Auth + Role Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('beranda');
    
    // Profil & Keamanan Admin RT
    Route::get('/pengaturan', [AdminProfilController::class, 'index'])->name('pengaturan');
    Route::post('/pengaturan/kontak', [AdminProfilController::class, 'updateKontak'])->name('pengaturan.kontak');
    Route::post('/pengaturan/password', [AdminProfilController::class, 'updatePassword'])->name('pengaturan.password');

    // Kelola Pengumuman
    Route::get('/pengumuman', [PengumumanController::class, 'TampilkanPengumuman'])->name('pengumuman');
    Route::get('/pengumuman/tambah', [PengumumanController::class, 'CreatePengumuman'])->name('pengumuman.create');
    Route::post('/pengumuman/tambah', [PengumumanController::class, 'simpanPengumuman'])->name('pengumuman.store');
    Route::get('/pengumuman/edit/{id}', [PengumumanController::class, 'editPengumuman'])->name('pengumuman.edit');
    Route::post('/pengumuman/edit/{id}', [PengumumanController::class, 'updatePengumuman'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'hapusPengumuman'])->name('pengumuman.destroy');

    // Kelola Data Warga & Kartu Keluarga (UC-08, UC-11, F008, BR-02, BR-04, BR-08, BR-14)
    Route::get('/data-warga', [AdminWargaController::class, 'tampilkanDaftarWargaDanVerifikasi'])->name('data-warga');
    
    // Tambah Anggota Keluarga (Layar 2.2.12 / BR-14)
    Route::get('/data-warga/tambah-anggota', [AdminWargaController::class, 'tampilkanFormTambahAnggota'])->name('data-warga.tambah-anggota');
    Route::get('/data-warga/{no_kk}/anggota/tambah', [AdminWargaController::class, 'tampilkanFormTambahAnggota'])->name('data-warga.tambah-anggota-kk');
    Route::post('/data-warga/tambah-anggota', [AdminWargaController::class, 'simpanTambahAnggota'])->name('data-warga.tambah-anggota.store');
    Route::post('/data-warga/{no_kk}/anggota/tambah', [AdminWargaController::class, 'simpanTambahAnggota'])->name('data-warga.tambah-anggota-kk.store');

    // Edit Data Warga (Layar 2.2.10 / BR-02)
    Route::get('/data-warga/edit-anggota/{nik}', [AdminWargaController::class, 'tampilkanFormEditAnggota'])->name('data-warga.edit-anggota');
    Route::post('/data-warga/edit-anggota/{nik?}', [AdminWargaController::class, 'updateDataAnggota'])->name('data-warga.edit-anggota.store');

    // Detail KK & Aksi Keluarga (Layar 2.2.8)
    Route::get('/data-warga/{no_kk}', [AdminWargaController::class, 'tampilkanDetailKeluarga'])->name('data-warga.detail');
    Route::post('/data-warga/{no_kk}/status', [AdminWargaController::class, 'ubahStatusKeluarga'])->name('data-warga.status');
    Route::post('/data-warga/{no_kk}/reset-password', [AdminWargaController::class, 'resetPasswordKeluarga'])->name('data-warga.reset-password');
    Route::delete('/data-warga/anggota/{nik}', [AdminWargaController::class, 'hapusAnggota'])->name('data-warga.hapus-anggota');
    Route::delete('/data-warga/{no_kk}', [AdminWargaController::class, 'hapusKeluarga'])->name('data-warga.hapus-kk');
    Route::post('/data-warga/verifikasi/{id}', [AdminWargaController::class, 'verifikasiPengajuan'])->name('data-warga.verifikasi');

    // Kelola & Verifikasi Pengajuan Surat (UC-12, F013, BR-05, BR-07)
    Route::get('/surat', [AdminSuratController::class, 'index'])->name('surat');
    Route::get('/surat/preview/{id}', [AdminSuratController::class, 'preview'])->name('surat.preview');
    Route::post('/surat/{id}/setujui', [AdminSuratController::class, 'setujui'])->name('surat.setujui');
    Route::post('/surat/{id}/tolak', [AdminSuratController::class, 'tolak'])->name('surat.tolak');

    // Kelola & Rekap Iuran Warga (UC-10, F009, F010, BR-06)
    Route::get('/iuran', [AdminIuranController::class, 'index'])->name('iuran');
    Route::post('/iuran/pelunasan/{id}', [AdminIuranController::class, 'tandaiLunas'])->name('iuran.pelunasan');
    Route::post('/iuran/atur-standar', [AdminIuranController::class, 'aturStandarIuran'])->name('iuran.atur-standar');
});
