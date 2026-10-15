<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use App\Models\Keluarga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IuranController extends Controller
{
    /**
     * Nama-nama bulan dalam bahasa Indonesia
     */
    protected array $daftarBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Singkatan nama bulan
     */
    protected array $daftarBulanSingkat = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];

    /**
     * Menampilkan halaman kelola dan rekap iuran warga (Layar 2.2.16 / UC-10 / BR-06)
     */
    public function index(Request $request): View
    {
        $now = Carbon::now('Asia/Jakarta');
        $tahunBerjalan = (int) $request->input('tahun', $now->year);
        $bulanBerjalan = (int) $request->input('bulan', $now->month);

        // 1. Eksekusi Lazy generation 12 bulan sesuai BR-06
        $this->generateLazyIuranTahunBerjalan($tahunBerjalan);

        // 2. Metrik Statistik Kas & Status Pembayaran
        $totalPenerimaanKas = (float) Iuran::where('status', 'lunas')->sum('nominal');

        $totalKkLunasBulanIni = Iuran::where('status', 'lunas')
            ->where('bulan', $bulanBerjalan)
            ->where('tahun', $tahunBerjalan)
            ->distinct('keluarga_id')
            ->count('keluarga_id');

        $totalKkBelumBulanIni = Iuran::where('status', 'belum_lunas')
            ->where('bulan', $bulanBerjalan)
            ->where('tahun', $tahunBerjalan)
            ->distinct('keluarga_id')
            ->count('keluarga_id');

        // Jika bulan ini belum ada data tagihan, hitung total KK yang belum lunas di tahun berjalan
        $totalTunggakanKeseluruhan = Iuran::where('status', 'belum_lunas')
            ->where('tahun', $tahunBerjalan)
            ->where('bulan', '<=', $bulanBerjalan)
            ->count();

        $nominalWajibSaatIni = $this->ambilNominalTerkini();

        $catatanTerkini = Iuran::whereNotNull('catatan')
            ->where('catatan', '!=', '')
            ->latest('updated_at')
            ->value('catatan') ?? 'Iuran operasional kebersihan sampah, keamanan pos ronda 24 jam, & kas sosial RT 04.';

        // 3. Tab 1: Query Data Tunggakan KK (Belum Lunas)
        $tunggakanQuery = Iuran::with([
            'keluarga.kepalaKeluarga' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
            'keluarga.wargas' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
        ])->where('status', 'belum_lunas')
          ->where('tahun', $tahunBerjalan);

        // Filter pencarian teks tunggakan
        if ($request->filled('search_tunggakan')) {
            $q = trim($request->input('search_tunggakan'));
            $tunggakanQuery->where(function ($query) use ($q) {
                $query->whereHas('keluarga', function ($kq) use ($q) {
                    $kq->where('no_kk', 'like', "%{$q}%")
                        ->orWhereHas('wargas', function ($wq) use ($q) {
                            $wq->where('nama', 'like', "%{$q}%")
                                ->orWhere('alamat', 'like', "%{$q}%");
                        });
                });
            });
        }

        // Filter status ekonomi tunggakan
        if ($request->filled('filter_ekonomi')) {
            $eko = $request->input('filter_ekonomi');
            $tunggakanQuery->whereHas('keluarga', function ($kq) use ($eko) {
                $kq->where('status_ekonomi', $eko);
            });
        }

        // Urutkan: Bulan Berjalan dulu (1), tunggakan lampau (2), lalu bulan mendatang (3)
        $daftarTunggakan = $tunggakanQuery
            ->orderByRaw("CASE 
                WHEN bulan = {$bulanBerjalan} THEN 1 
                WHEN bulan < {$bulanBerjalan} THEN 2 
                ELSE 3 
            END ASC")
            ->orderBy('bulan', 'desc')
            ->orderBy('keluarga_id', 'asc')
            ->get();

        // 4. Tab 2: Query Riwayat Pembayaran Kas (Lunas)
        $riwayatQuery = Iuran::with([
            'keluarga.kepalaKeluarga' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
            'keluarga.wargas' => function ($q) {
                $q->where('status_warga', 'hidup');
            },
        ])->where('status', 'lunas');

        // Filter pencarian riwayat
        if ($request->filled('search_riwayat')) {
            $rq = trim($request->input('search_riwayat'));
            $riwayatQuery->where(function ($query) use ($rq) {
                $query->whereHas('keluarga', function ($kq) use ($rq) {
                    $kq->where('no_kk', 'like', "%{$rq}%")
                        ->orWhereHas('wargas', function ($wq) use ($rq) {
                            $wq->where('nama', 'like', "%{$rq}%")
                                ->orWhere('alamat', 'like', "%{$rq}%");
                        });
                });
            });
        }

        // Filter bulan riwayat
        if ($request->filled('filter_bulan')) {
            $riwayatQuery->where('bulan', (int) $request->input('filter_bulan'));
        }

        // Filter tahun riwayat
        if ($request->filled('filter_tahun')) {
            $riwayatQuery->where('tahun', (int) $request->input('filter_tahun'));
        }

        $daftarRiwayat = $riwayatQuery
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        $totalTerverifikasiLunas = $daftarRiwayat->sum('nominal');

        $namaBulanBerjalan = $this->daftarBulan[$bulanBerjalan] ?? 'Bulan ' . $bulanBerjalan;
        $periodeAktifTeks = "{$namaBulanBerjalan} {$tahunBerjalan}";

        return view('admin.iuran', compact(
            'tahunBerjalan',
            'bulanBerjalan',
            'namaBulanBerjalan',
            'periodeAktifTeks',
            'totalPenerimaanKas',
            'totalKkLunasBulanIni',
            'totalKkBelumBulanIni',
            'totalTunggakanKeseluruhan',
            'nominalWajibSaatIni',
            'catatanTerkini',
            'daftarTunggakan',
            'daftarRiwayat',
            'totalTerverifikasiLunas'
        ));
    }

    /**
     * Mencatat pelunasan iuran warga (Layar 2.2.18 / BR-06)
     */
    public function tandaiLunas(Request $request, int $id)
    {
        $iuran = Iuran::with('keluarga')->findOrFail($id);

        $validated = $request->validate([
            'tanggal_bayar' => ['nullable', 'date'],
            'nominal' => ['nullable', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $tanggalBayar = $request->filled('tanggal_bayar')
            ? Carbon::parse($validated['tanggal_bayar'])->setTimeFrom(Carbon::now('Asia/Jakarta'))
            : Carbon::now('Asia/Jakarta');

        $iuran->update([
            'status' => 'lunas',
            'tanggal_bayar' => $tanggalBayar,
            'nominal' => $validated['nominal'] ?? $iuran->nominal,
            'catatan' => $validated['catatan'] ?? $iuran->catatan,
        ]);

        $bulanNama = $this->daftarBulan[$iuran->bulan] ?? 'Bulan ' . $iuran->bulan;
        $noKk = $iuran->keluarga?->no_kk ?? '-';

        return redirect()->back()->with('success', "Pembayaran iuran KK No. {$noKk} periode {$bulanNama} {$iuran->tahun} berhasil dicatat LUNAS.");
    }

    /**
     * Memperbarui besaran tarif standar iuran bulanan RT (Layar 2.2.17 / BR-06)
     */
    public function aturStandarIuran(Request $request)
    {
        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:1000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $now = Carbon::now('Asia/Jakarta');
        $tahunBerjalan = $now->year;
        $bulanBerjalan = $now->month;
        $nominalBaru = (float) $validated['nominal'];
        $catatanBaru = $validated['catatan'] ?? null;

        // BR-06: UPDATE baris belum_lunas mulai bulan berjalan sampai akhir tahun berjalan
        $query = Iuran::where('status', 'belum_lunas')
            ->where('tahun', $tahunBerjalan)
            ->where('bulan', '>=', $bulanBerjalan);

        $updateData = ['nominal' => $nominalBaru];
        if ($catatanBaru !== null) {
            $updateData['catatan'] = $catatanBaru;
        }

        $affected = $query->update($updateData);

        $namaBulan = $this->daftarBulan[$bulanBerjalan] ?? 'Bulan ' . $bulanBerjalan;
        $nominalFmt = 'Rp ' . number_format($nominalBaru, 0, ',', '.');

        return redirect()->back()->with('success', "Standar iuran bulanan berhasil diperbarui menjadi {$nominalFmt} / KK mulai periode {$namaBulan} {$tahunBerjalan} hingga akhir tahun ({$affected} baris diperbarui).");
    }

    /**
     * Implementasi BR-06: Lazy generation 12 bulan untuk KK aktif
     */
    public function generateLazyIuranTahunBerjalan(int $tahun): void
    {
        // Temukan KK yang aktif (memiliki minimal 1 anggota dengan status 'hidup')
        $keluargasAktif = Keluarga::whereHas('wargas', function ($q) {
            $q->where('status_warga', 'hidup');
        })->get();

        if ($keluargasAktif->isEmpty()) {
            return;
        }

        $nominalDefault = $this->ambilNominalTerkini();

        foreach ($keluargasAktif as $keluarga) {
            // Hitung bulan awal tagih sesuai BR-06:
            // Jika tahun created_at < tahun: Januari (1). Jika sama: bulan created_at.
            $createdYear = $keluarga->created_at ? $keluarga->created_at->year : $tahun;
            $createdMonth = $keluarga->created_at ? $keluarga->created_at->month : 1;

            $bulanAwalTagih = ($createdYear < $tahun) ? 1 : $createdMonth;

            // Generate baris belum_lunas dari bulan awal tagih sampai Desember (12)
            for ($b = $bulanAwalTagih; $b <= 12; $b++) {
                Iuran::firstOrCreate(
                    [
                        'keluarga_id' => $keluarga->id,
                        'bulan' => $b,
                        'tahun' => $tahun,
                    ],
                    [
                        'nominal' => $nominalDefault,
                        'status' => 'belum_lunas',
                        'catatan' => null,
                        'tanggal_bayar' => null,
                    ]
                );
            }
        }
    }

    /**
     * Menentukan sumber nominal baris baru sesuai prioritas BR-06
     */
    public function ambilNominalTerkini(): float
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

        // Prioritas 2: Jika tidak ada, baris terbaru berstatus apa pun
        $nomTerbaru = Iuran::orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('id', 'desc')
            ->value('nominal');

        if ($nomTerbaru !== null && (float) $nomTerbaru > 0) {
            return (float) $nomTerbaru;
        }

        // Prioritas 3: File konfigurasi config/wargaku.php (nilai default Rp 75.000)
        return (float) config('wargaku.nominal_iuran_awal', 75000);
    }
}
