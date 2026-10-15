<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Beranda Mandiri Warga (Layar 2.3.1)
     */
    public function index(): View
    {
        $user = auth()->user();

        // Ambil data kepala keluarga / warga yang terikat pada akun
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        // Periode berjalan
        $now = Carbon::now('Asia/Jakarta');
        $bulanBerjalan = $now->month;
        $tahunBerjalan = $now->year;

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = $daftarBulan[$bulanBerjalan] ?? 'Bulan ' . $bulanBerjalan;

        // 1. Data Iuran Bulan Ini
        $nominalIuran = config('wargaku.nominal_iuran_awal', 75000);
        $isIuranLunas = false;
        $iuranBulanIni = null;

        if ($keluarga) {
            $iuranBulanIni = $keluarga->iurans()
                ->where('bulan', $bulanBerjalan)
                ->where('tahun', $tahunBerjalan)
                ->first();

            if ($iuranBulanIni) {
                $nominalIuran = (float) $iuranBulanIni->nominal;
                $isIuranLunas = ($iuranBulanIni->status === 'lunas');
            }
        }

        // 2. Data Statistik Pengajuan Surat Keluarga
        $wargaIds = $keluarga ? $keluarga->wargas()->pluck('id')->toArray() : [];
        if (empty($wargaIds) && $kepala) {
            $wargaIds = [$kepala->id];
        }

        $totalSuratMenunggu = 0;
        $totalSuratSelesai = 0;
        $riwayatSuratTerakhir = collect();

        if (!empty($wargaIds)) {
            $totalSuratMenunggu = Surat::where(function ($q) use ($wargaIds) {
                    $q->whereIn('pemohon_id', $wargaIds)
                      ->orWhereIn('warga_tujuan_id', $wargaIds);
                })
                ->where('status', 'menunggu')
                ->count();

            $totalSuratSelesai = Surat::where(function ($q) use ($wargaIds) {
                    $q->whereIn('pemohon_id', $wargaIds)
                      ->orWhereIn('warga_tujuan_id', $wargaIds);
                })
                ->where('status', 'disetujui')
                ->count();

            $riwayatSuratTerakhir = Surat::with(['pemohon', 'wargaTujuan'])
                ->where(function ($q) use ($wargaIds) {
                    $q->whereIn('pemohon_id', $wargaIds)
                      ->orWhereIn('warga_tujuan_id', $wargaIds);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('warga.beranda', compact(
            'user',
            'kepala',
            'keluarga',
            'namaBulan',
            'tahunBerjalan',
            'nominalIuran',
            'isIuranLunas',
            'iuranBulanIni',
            'totalSuratMenunggu',
            'totalSuratSelesai',
            'riwayatSuratTerakhir'
        ));
    }
}
