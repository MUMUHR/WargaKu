<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use App\Models\Keluarga;
use App\Models\Pengumuman;
use App\Models\Riwayat_Perubahan;
use App\Models\Surat;
use App\Models\Warga;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama Admin RT dengan data riil dari database.
     */
    public function index(): View
    {
        $totalKk = Keluarga::count();
        $totalWargaHidup = Warga::where('status_warga', 'hidup')->count();

        $pengajuanDataMenunggu = Riwayat_Perubahan::where('status', 'menunggu')->count();
        $pengajuanSuratMenunggu = Surat::where('status', 'menunggu')->count();
        $totalPengajuanMenunggu = $pengajuanDataMenunggu + $pengajuanSuratMenunggu;

        $bulanSekarang = (int) now()->format('n');
        $tahunSekarang = (int) now()->format('Y');
        $namaBulanTahun = now()->translatedFormat('F Y');

        $iuranTerkumpulBulanIni = Iuran::where('status', 'lunas')
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->sum('nominal');

        $pengumumanTerbaru = Pengumuman::with('admin')
            ->latest()
            ->take(3)
            ->get();

        $totalPengumuman = Pengumuman::count();

        return view('admin.beranda', compact(
            'totalKk',
            'totalWargaHidup',
            'totalPengajuanMenunggu',
            'iuranTerkumpulBulanIni',
            'namaBulanTahun',
            'pengumumanTerbaru',
            'totalPengumuman'
        ));
    }
}
