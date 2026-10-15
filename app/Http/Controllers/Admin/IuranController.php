<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Iuran;
use App\Services\IuranService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IuranController extends Controller
{
    /**
     * Menampilkan halaman kelola dan rekap iuran warga (Layar 2.2.16 / UC-10 / BR-06)
     */
    public function index(Request $request, IuranService $iuranService): View
    {
        $now           = Carbon::now('Asia/Jakarta');
        $tahunBerjalan = (int) $request->input('tahun', $now->year);
        $bulanBerjalan = (int) $request->input('bulan', $now->month);

        // 1. Eksekusi Lazy generation 12 bulan sesuai BR-06 untuk tahun berjalan via IuranService
        $iuranService->generateLazyIuranSemuaKeluargaAktif($now->year);

        // 2. Metrik Statistik Kas & Status Pembayaran via IuranService
        $stat = $iuranService->hitungStatistikAdmin($tahunBerjalan, $bulanBerjalan);
        $totalPenerimaanKas        = $stat['totalPenerimaanKas'];
        $totalKkLunasBulanIni      = $stat['totalKkLunasBulanIni'];
        $totalKkBelumBulanIni      = $stat['totalKkBelumBulanIni'];
        $totalTunggakanKeseluruhan = $stat['totalTunggakanKeseluruhan'];

        $nominalWajibSaatIni = $iuranService->ambilNominalStandar();
        $catatanTerkini      = $iuranService->ambilCatatanTarifAdmin();

        // Eager load relasi keluarga & anggota berstatus hidup (BR-13 & 0 N+1 Query)
        $relasiKeluargaHidup = [
            'keluarga.kepalaKeluarga' => fn($q) => $q->where('status_warga', 'hidup'),
            'keluarga.wargas'         => fn($q) => $q->where('status_warga', 'hidup'),
        ];

        // 3. Tab 1: Query Data Tunggakan KK (Belum Lunas)
        $tunggakanQuery = Iuran::with($relasiKeluargaHidup)
            ->where('status', 'belum_lunas')
            ->where('tahun', $tahunBerjalan);

        // Filter pencarian teks tunggakan
        if ($request->filled('search_tunggakan')) {
            $this->terapkanFilterPencarian($tunggakanQuery, $request->input('search_tunggakan'));
        }

        // Filter status ekonomi tunggakan
        if ($request->filled('filter_ekonomi')) {
            $eko = $request->input('filter_ekonomi');
            $tunggakanQuery->whereHas('keluarga', function ($kq) use ($eko) {
                $kq->where('status_ekonomi', $eko);
            });
        }

        // Urutkan: Bulan Berjalan dulu (1), tunggakan lampau (2), lalu bulan mendatang (3) dengan parameter binding
        $daftarTunggakan = $tunggakanQuery
            ->orderByRaw("CASE WHEN bulan = ? THEN 1 WHEN bulan < ? THEN 2 ELSE 3 END ASC", [$bulanBerjalan, $bulanBerjalan])
            ->orderBy('bulan', 'desc')
            ->orderBy('keluarga_id', 'asc')
            ->get();

        // 4. Tab 2: Query Riwayat Pembayaran Kas (Lunas)
        $riwayatQuery = Iuran::with($relasiKeluargaHidup)
            ->where('status', 'lunas');

        // Filter pencarian riwayat
        if ($request->filled('search_riwayat')) {
            $this->terapkanFilterPencarian($riwayatQuery, $request->input('search_riwayat'));
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

        $namaBulanBerjalan = IuranService::DAFTAR_BULAN[$bulanBerjalan] ?? 'Bulan ' . $bulanBerjalan;
        $periodeAktifTeks  = "{$namaBulanBerjalan} {$tahunBerjalan}";

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
    public function tandaiLunas(Request $request, int $id, IuranService $iuranService)
    {
        $iuran = Iuran::with('keluarga')->findOrFail($id);

        $validated = $request->validate([
            'tanggal_bayar' => ['nullable', 'date'],
            'nominal'       => ['nullable', 'numeric', 'min:0'],
            'catatan'       => ['nullable', 'string', 'max:500'],
        ]);

        $nominal = isset($validated['nominal']) 
            ? (float) $validated['nominal'] 
            : null;

        $iuranService->catatPelunasan(
            $iuran,
            $validated['tanggal_bayar'] ?? null,
            $nominal,
            $validated['catatan'] ?? null
        );

        $bulanNama = IuranService::DAFTAR_BULAN[$iuran->bulan] ?? 'Bulan ' . $iuran->bulan;
        $noKk = $iuran->keluarga?->no_kk ?? '-';

        return redirect()->back()->with('success', "Pembayaran iuran KK No. {$noKk} periode {$bulanNama} {$iuran->tahun} berhasil dicatat LUNAS.");
    }

    /**
     * Memperbarui besaran tarif standar iuran bulanan RT (Layar 2.2.17 / BR-06)
     */
    public function aturStandarIuran(Request $request, IuranService $iuranService)
    {
        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:1000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $now           = Carbon::now('Asia/Jakarta');
        $tahunBerjalan = $now->year;
        $bulanBerjalan = $now->month;
        $nominalBaru   = (float) $validated['nominal'];
        $catatanBaru   = isset($validated['catatan']) ? trim($validated['catatan']) : null;

        // BR-06: UPDATE baris belum_lunas mulai bulan berjalan sampai akhir tahun via IuranService
        $affected = $iuranService->aturStandarIuran($nominalBaru, $catatanBaru, $tahunBerjalan, $bulanBerjalan);

        $namaBulan = IuranService::DAFTAR_BULAN[$bulanBerjalan] ?? 'Bulan ' . $bulanBerjalan;
        $nominalFmt = 'Rp ' . number_format($nominalBaru, 0, ',', '.');

        return redirect()->back()->with('success', "Standar iuran bulanan berhasil diperbarui menjadi {$nominalFmt} / KK mulai periode {$namaBulan} {$tahunBerjalan} hingga akhir tahun ({$affected} baris diperbarui).");
    }

    /**
     * Filter pencarian No. KK, nama anggota hidup, atau alamat (BR-13)
     */
    private function terapkanFilterPencarian(Builder $query, string $keyword): void
    {
        $q = trim($keyword);
        if ($q === '') {
            return;
        }

        $query->whereHas('keluarga', function ($kq) use ($q) {
            $kq->where('no_kk', 'like', "%{$q}%")
               ->orWhereHas('wargas', function ($wq) use ($q) {
                   $wq->where('status_warga', 'hidup')
                      ->where(function ($sub) use ($q) {
                          $sub->where('nama', 'like', "%{$q}%")
                              ->orWhere('alamat', 'like', "%{$q}%");
                      });
               });
        });
    }
}
