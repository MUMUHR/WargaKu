<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use App\Services\IuranService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IuranController extends Controller
{
    /**
     * Menampilkan halaman monitoring 12 card iuran bulanan warga (Layar 2.2.26 / UC-05 / BR-06)
     */
    public function index(Request $request, IuranService $iuranService): View
    {
        $user = auth()->user();

        // 1. Ambil data keluarga & kepala keluarga secara efisien
        $wargaAkun    = $user->wargas()->with('keluarga.wargas')->first();
        $keluarga     = $wargaAkun?->keluarga;
        $anggotaHidup = $keluarga ? $keluarga->wargas->where('status_warga', 'hidup') : collect();

        $kepala = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga')
            ?? $anggotaHidup->firstWhere('nik', $user->username)
            ?? $anggotaHidup->first()
            ?? $wargaAkun;

        // Waktu acuan periode berjalan (Asia/Jakarta sesuai BR-06)
        $now = Carbon::now('Asia/Jakarta');
        $tahunBerjalan = $now->year;
        $bulanBerjalan = $now->month;

        // Tahun yang dipilih warga pada filter (default: tahun berjalan)
        $tahunDipilih = (int) $request->input('tahun', $tahunBerjalan);

        // Pastikan lazy generation 12 bulan hanya berjalan untuk tahun berjalan (BR-06)
        if ($keluarga) {
            $iuranService->generateLazyIuranKeluarga($keluarga, $tahunBerjalan);
        }

        // Ambil daftar tahun yang tersedia untuk filter dropdown
        $daftarTahun = $keluarga 
            ? Iuran::where('keluarga_id', $keluarga->id)->distinct()->pluck('tahun')->toArray()
            : [];
        if (!in_array($tahunBerjalan, $daftarTahun)) {
            $daftarTahun[] = $tahunBerjalan;
        }
        rsort($daftarTahun);

        // Susun 12 card bulan via IuranService (BR-06)
        $cards12Bulan = $keluarga
            ? $iuranService->formatCards12Bulan($keluarga, $tahunDipilih, $tahunBerjalan, $bulanBerjalan)
            : [];

        // Tarif Iuran Acuan RT & Catatan Rincian Dana dari Admin RT via IuranService
        $tarifStandar      = $iuranService->ambilNominalStandar();
        $catatanTarifAdmin = $iuranService->ambilCatatanTarifAdmin();

        // Data Ringkasan Banner Atas: Status Bulan Berjalan
        $iuranBulanBerjalan = $keluarga 
            ? $keluarga->iurans()
                ->where('tahun', $tahunBerjalan)
                ->where('bulan', $bulanBerjalan)
                ->first()
            : null;

        $isBulanIniLunas      = $iuranBulanBerjalan?->status === 'lunas';
        $nominalBulanBerjalan = $iuranBulanBerjalan ? (float) $iuranBulanBerjalan->nominal : $tarifStandar;

        return view('warga.iuran', compact(
            'keluarga',
            'kepala',
            'tahunBerjalan',
            'bulanBerjalan',
            'tahunDipilih',
            'daftarTahun',
            'cards12Bulan',
            'isBulanIniLunas',
            'nominalBulanBerjalan',
            'tarifStandar',
            'catatanTarifAdmin'
        ));
    }
}
