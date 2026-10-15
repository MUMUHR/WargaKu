<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Services\IuranService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Beranda Mandiri Warga (Layar 2.2.19)
     */
    public function index(IuranService $iuranService): View
    {
        $user = auth()->user();

        // 1. Ambil data Keluarga & Kepala Keluarga secara efisien (BR-01 & BR-13)
        $wargaAkun = $user->wargas()->with('keluarga')->first();
        $keluarga  = $wargaAkun?->keluarga;

        $anggotaKeluarga = $keluarga
            ? $keluarga->wargas()->get()
            : collect($wargaAkun ? [$wargaAkun] : []);

        $anggotaHidup = $anggotaKeluarga->where('status_warga', 'hidup');

        // Prioritaskan Kepala Keluarga yang hidup, fallback ke pemilik akun (nik == username), lalu anggota pertama
        $kepala = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga')
            ?? $anggotaHidup->firstWhere('nik', $user->username)
            ?? $anggotaHidup->first()
            ?? $wargaAkun;

        // 2. Periode Berjalan (Zona Waktu Asia/Jakarta - BR-06)
        $now           = Carbon::now('Asia/Jakarta')->locale('id');
        $bulanBerjalan = $now->month;
        $tahunBerjalan = $now->year;
        $namaBulan     = $now->translatedFormat('F');

        // 3. Data Ringkasan Iuran Bulan Ini (Sesuai Prioritas Nominal BR-06 via IuranService)
        $iuranBulanIni = null;
        $isIuranLunas  = false;
        $nominalIuran  = $iuranService->ambilNominalStandar();

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

        // 4. Data Statistik Pengajuan Surat & Perubahan Data Keluarga (UC-07, UC-09, F017)
        $wargaIds = $anggotaKeluarga->pluck('id')->all();

        $totalSuratMenunggu         = 0;
        $totalSuratSelesai          = 0;
        $totalPerubahanDataMenunggu = 0;
        $riwayatSuratTerakhir       = collect();

        if (!empty($wargaIds)) {
            $baseSuratQuery = Surat::where(function ($q) use ($wargaIds) {
                $q->whereIn('pemohon_id', $wargaIds)
                  ->orWhereIn('warga_tujuan_id', $wargaIds);
            });

            $totalSuratMenunggu = (clone $baseSuratQuery)->where('status', 'menunggu')->count();
            $totalSuratSelesai  = (clone $baseSuratQuery)->where('status', 'disetujui')->count();

            $riwayatSuratTerakhir = (clone $baseSuratQuery)
                ->with(['pemohon', 'wargaTujuan'])
                ->latest()
                ->take(5)
                ->get();
        }

        if ($keluarga) {
            $totalPerubahanDataMenunggu = $keluarga->riwayatPerubahan()
                ->where('status', 'menunggu')
                ->count();
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
            'totalPerubahanDataMenunggu',
            'riwayatSuratTerakhir'
        ));
    }
}
