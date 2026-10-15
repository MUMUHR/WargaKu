<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use App\Models\Keluarga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IuranController extends Controller
{
    /**
     * Nama-nama bulan kalender dalam bahasa Indonesia
     */
    private array $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Menampilkan halaman monitoring 12 card iuran bulanan warga (Layar 2.2.26 / UC-05 / BR-06)
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Ambil data kepala keluarga dan relasi keluarga dari akun warga yang login
        $kepala = $user->wargaPemilik 
            ?? $user->wargas()->where('status_hubungan', 'kepala_keluarga')->first() 
            ?? $user->wargas()->first();
        $keluarga = $kepala?->keluarga;

        // Waktu acuan periode berjalan (Asia/Jakarta sesuai BR-06)
        $now = Carbon::now('Asia/Jakarta');
        $tahunBerjalan = $now->year;
        $bulanBerjalan = $now->month;

        // Tahun yang dipilih warga pada filter (default: tahun berjalan)
        $tahunDipilih = (int) $request->input('tahun', $tahunBerjalan);

        // Pastikan lazy generation 12 bulan berjalan untuk keluarga aktif (BR-06)
        if ($keluarga && $keluarga->wargas()->where('status_warga', 'hidup')->exists()) {
            $this->pastikanIuranTersedia($keluarga, $tahunDipilih);
        }

        // Ambil daftar tahun yang tersedia untuk filter dropdown
        $daftarTahun = $keluarga 
            ? Iuran::where('keluarga_id', $keluarga->id)->distinct()->pluck('tahun')->toArray()
            : [];
        if (!in_array($tahunBerjalan, $daftarTahun)) {
            $daftarTahun[] = $tahunBerjalan;
        }
        rsort($daftarTahun);

        // Ambil data iuran 12 bulan untuk keluarga pada tahun yang dipilih
        $iuranRecords = $keluarga
            ? Iuran::where('keluarga_id', $keluarga->id)
                ->where('tahun', $tahunDipilih)
                ->get()
                ->keyBy('bulan')
            : collect();

        // Hitung bulan awal tagih sesuai BR-06
        $createdYear = $keluarga && $keluarga->created_at ? $keluarga->created_at->year : $tahunDipilih;
        $createdMonth = $keluarga && $keluarga->created_at ? $keluarga->created_at->month : 1;
        $bulanAwalTagih = ($createdYear < $tahunDipilih) ? 1 : $createdMonth;

        // Susun struktur data 12 card bulan (Januari s.d. Desember)
        $cards12Bulan = [];
        for ($b = 1; $b <= 12; $b++) {
            $namaBln = $this->namaBulan[$b] ?? "Bulan {$b}";
            $record = $iuranRecords->get($b);

            // Kondisi 1: Bulan sebelum KK terdaftar (N/A Dikecualikan)
            if (!$record && $b < $bulanAwalTagih) {
                $cards12Bulan[] = [
                    'bulan'                => $b,
                    'nama_bulan'           => "{$namaBln} {$tahunDipilih}",
                    'nominal_format'       => '-',
                    'status'               => 'na',
                    'badge_label'          => 'N/A',
                    'badge_class'          => 'month-badge--na',
                    'penanda_berjalan'     => false,
                    'info_tanggal'         => '-',
                    'is_lunas'             => false,
                    'footer_text'          => 'Dikecualikan dari Kewajiban',
                    'catatan'              => 'Dikecualikan dari kewajiban iuran',
                    'card_modifier_class'  => 'month-card--na',
                ];
                continue;
            }

            // Jika record iuran ada
            $isLunas = $record && $record->status === 'lunas';
            $nominalValue = $record ? (float) $record->nominal : (float) config('wargaku.nominal_iuran_awal', 75000);
            $nominalFormat = 'Rp ' . number_format($nominalValue, 0, ',', '.');
            $catatanPembukuan = $record && !empty($record->catatan) 
                ? $record->catatan 
                : 'Iuran Kebersihan & Keamanan RT 04';

            if ($isLunas) {
                // Kondisi 2: LUNAS
                $tglBayar = $record->tanggal_bayar 
                    ? Carbon::parse($record->tanggal_bayar)->translatedFormat('d M Y') 
                    : '-';

                $cards12Bulan[] = [
                    'bulan'                => $b,
                    'nama_bulan'           => "{$namaBln} {$tahunDipilih}",
                    'nominal_format'       => $nominalFormat,
                    'status'               => 'lunas',
                    'badge_label'          => 'LUNAS',
                    'badge_class'          => 'month-badge--lunas',
                    'penanda_berjalan'     => false,
                    'info_tanggal'         => "Dibayar: {$tglBayar}",
                    'is_lunas'             => true,
                    'footer_text'          => 'Kas Wajib Bulanan RT 04',
                    'catatan'              => $catatanPembukuan,
                    'card_modifier_class'  => '',
                ];
            } else {
                // Kondisi 3, 4, 5: BELUM LUNAS
                $isBulanBerjalan = ($tahunDipilih === $tahunBerjalan && $b === $bulanBerjalan);
                $isMenunggak = ($tahunDipilih < $tahunBerjalan) || ($tahunDipilih === $tahunBerjalan && $b < $bulanBerjalan);

                if ($isBulanBerjalan) {
                    $badgeLabel = 'BELUM LUNAS';
                    $penandaBerjalan = true;
                    $cardModifier = 'month-card--active';
                } elseif ($isMenunggak) {
                    $badgeLabel = 'MENUNGGAK';
                    $penandaBerjalan = false;
                    $cardModifier = 'month-card--menunggak';
                } else {
                    $badgeLabel = 'BELUM LUNAS';
                    $penandaBerjalan = false;
                    $cardModifier = '';
                }

                $cards12Bulan[] = [
                    'bulan'                => $b,
                    'nama_bulan'           => "{$namaBln} {$tahunDipilih}",
                    'nominal_format'       => $nominalFormat,
                    'status'               => 'belum_lunas',
                    'badge_label'          => $badgeLabel,
                    'badge_class'          => $isMenunggak ? 'month-badge--menunggak' : 'month-badge--belum',
                    'penanda_berjalan'     => $penandaBerjalan,
                    'info_tanggal'         => '-',
                    'is_lunas'             => false,
                    'footer_text'          => 'Kas Wajib Bulanan RT 04',
                    'catatan'              => $catatanPembukuan,
                    'card_modifier_class'  => $cardModifier,
                ];
            }
        }

        // Data Ringkasan Banner Atas: Status Bulan Berjalan
        $iuranBulanBerjalan = $keluarga 
            ? Iuran::where('keluarga_id', $keluarga->id)
                ->where('tahun', $tahunBerjalan)
                ->where('bulan', $bulanBerjalan)
                ->first()
            : null;

        $isBulanIniLunas = $iuranBulanBerjalan && $iuranBulanBerjalan->status === 'lunas';
        $nominalBulanBerjalan = $iuranBulanBerjalan 
            ? (float) $iuranBulanBerjalan->nominal 
            : (float) config('wargaku.nominal_iuran_awal', 75000);

        // Tarif Iuran Acuan RT
        $tarifStandar = $this->ambilNominalTerkini();

        // Catatan rincian alokasi dana iuran yang ditentukan oleh Admin RT
        $catatanTarifAdmin = Iuran::where('status', 'belum_lunas')
            ->whereNotNull('catatan')
            ->where('catatan', '!=', '')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->value('catatan')
            ?? Iuran::whereNotNull('catatan')
                ->where('catatan', '!=', '')
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->value('catatan')
            ?? 'Iuran operasional kebersihan sampah, keamanan pos ronda 24 jam, & kas sosial RT 04.';

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

    /**
     * Lazy generation tagihan 12 bulan untuk KK yang sedang mengakses iuran (BR-06)
     */
    private function pastikanIuranTersedia(Keluarga $keluarga, int $tahun): void
    {
        $createdYear = $keluarga->created_at ? $keluarga->created_at->year : $tahun;
        $createdMonth = $keluarga->created_at ? $keluarga->created_at->month : 1;
        $bulanAwalTagih = ($createdYear < $tahun) ? 1 : $createdMonth;

        $nominalDefault = $this->ambilNominalTerkini();

        for ($b = $bulanAwalTagih; $b <= 12; $b++) {
            Iuran::firstOrCreate(
                [
                    'keluarga_id' => $keluarga->id,
                    'bulan'       => $b,
                    'tahun'       => $tahun,
                ],
                [
                    'nominal'       => $nominalDefault,
                    'status'        => 'belum_lunas',
                    'catatan'       => null,
                    'tanggal_bayar' => null,
                ]
            );
        }
    }

    /**
     * Menentukan besaran nominal baris baru sesuai aturan prioritas BR-06
     */
    private function ambilNominalTerkini(): float
    {
        // Prioritas 1: Baris terbaru di database berstatus 'belum_lunas'
        $nomBelumLunas = Iuran::where('status', 'belum_lunas')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('id', 'desc')
            ->value('nominal');

        if ($nomBelumLunas !== null && (float) $nomBelumLunas > 0) {
            return (float) $nomBelumLunas;
        }

        // Prioritas 2: Baris terbaru berstatus apa pun
        $nomTerbaru = Iuran::orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('id', 'desc')
            ->value('nominal');

        if ($nomTerbaru !== null && (float) $nomTerbaru > 0) {
            return (float) $nomTerbaru;
        }

        // Prioritas 3: File konfigurasi config/wargaku.php
        return (float) config('wargaku.nominal_iuran_awal', 75000);
    }
}
