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
     * Menampilkan halaman kelola dan rekap iuran warga (Layar 2.2.16 / UC-10 / BR-06).
     *
     * Method ini untuk memuat statistik keuangan RT, daftar tunggakan kartu keluarga (Tab 1),
     * serta riwayat penerimaan kas yang telah terverifikasi lunas (Tab 2).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IuranService  $iuranService
     * @return \Illuminate\View\View
     */
    public function index(Request $request, IuranService $iuranService): View
    {
        // 1. Mengambil waktu real-time dengan zona waktu WIB (Asia/Jakarta)
        $now           = Carbon::now('Asia/Jakarta');

        // 2. Mengambil filter tahun dari request user, atau default ke tahun saat ini
        $tahunBerjalan = (int) $request->input('tahun', $now->year);

        // 3. Mengambil filter bulan dari request user, atau default ke bulan saat ini (1 - 12)
        $bulanBerjalan = (int) $request->input('bulan', $now->month);

        // 4. Menjalankan mekanisme Lazy Generation (BR-06):
        // Memastikan seluruh KK yang aktif telah memiliki tagihan 12 bulan di database tanpa duplikasi
        $iuranService->generateLazyIuranSemuaKeluargaAktif($now->year);

        // 5. Menghitung ringkasan statistik kas RT (Penerimaan kas, KK lunas bulan ini, KK belum lunas, dan total tunggakan)
        $stat = $iuranService->hitungStatistikAdmin($tahunBerjalan, $bulanBerjalan);
        $totalPenerimaanKas        = $stat['totalPenerimaanKas'];
        $totalKkLunasBulanIni      = $stat['totalKkLunasBulanIni'];
        $totalKkBelumBulanIni      = $stat['totalKkBelumBulanIni'];
        $totalTunggakanKeseluruhan = $stat['totalTunggakanKeseluruhan'];

        // 6. Mengambil besaran nominal iuran standar yang sedang berlaku saat ini
        $nominalWajibSaatIni = $iuranService->ambilNominalStandar();

        // 7. Mengambil catatan penjelasan kebijakan tarif yang ditulis oleh admin (jika ada)
        $catatanTerkini      = $iuranService->ambilCatatanTarifAdmin();

        // 8. Menyiapkan eager loading relasi keluarga & anggota keluarga yang masih HIDUP
        // Hal ini penting untuk optimasi query (mencegah N+1 query) dan memastikan warga meninggal tidak dihitung
        $relasiKeluargaHidup = [
            'keluarga.kepalaKeluarga' => fn($q) => $q->where('status_warga', 'hidup'),
            'keluarga.wargas'         => fn($q) => $q->where('status_warga', 'hidup'),
        ];

        // ══════════════════════════════════════════════════════════════════
        // TAB 1: QUERY DAFTAR TUNGGAKAN WARGA (STATUS: BELUM_LUNAS)
        // ══════════════════════════════════════════════════════════════════
        $tunggakanQuery = Iuran::with($relasiKeluargaHidup)
            ->where('status', 'belum_lunas')
            ->where('tahun', $tahunBerjalan);

        // Menyaring data tunggakan jika ada kata kunci pencarian (No. KK / Nama / Alamat)
        if ($request->filled('search_tunggakan')) {
            $this->terapkanFilterPencarian($tunggakanQuery, $request->input('search_tunggakan'));
        }

        // Menyaring data tunggakan berdasarkan filter kategori ekonomi (mampu / kurang mampu)
        if ($request->filled('filter_ekonomi')) {
            $eko = $request->input('filter_ekonomi');
            $tunggakanQuery->whereHas('keluarga', function ($kq) use ($eko) {
                $kq->where('status_ekonomi', $eko);
            });
        }

        // Mengurutkan prioritas tampil tabel tunggakan:
        // Prioritas 1: Tagihan bulan berjalan saat ini (paling urgen)
        // Prioritas 2: Tagihan bulan-bulan yang sudah lewat (menunggak)
        // Prioritas 3: Tagihan bulan-bulan mendatang
        $daftarTunggakan = $tunggakanQuery
            ->orderByRaw("CASE WHEN bulan = ? THEN 1 WHEN bulan < ? THEN 2 ELSE 3 END ASC", [$bulanBerjalan, $bulanBerjalan])
            ->orderBy('bulan', 'desc')
            ->orderBy('keluarga_id', 'asc')
            ->get();

        // ══════════════════════════════════════════════════════════════════
        // TAB 2: QUERY RIWAYAT PENERIMAAN KAS RT (STATUS: LUNAS)
        // ══════════════════════════════════════════════════════════════════
        $riwayatQuery = Iuran::with($relasiKeluargaHidup)
            ->where('status', 'lunas');

        // Menyaring data riwayat jika ada kata kunci pencarian teks
        if ($request->filled('search_riwayat')) {
            $this->terapkanFilterPencarian($riwayatQuery, $request->input('search_riwayat'));
        }

        // Menyaring data riwayat berdasarkan bulan pembayaran tertentu jika dipilih
        if ($request->filled('filter_bulan')) {
            $riwayatQuery->where('bulan', (int) $request->input('filter_bulan'));
        }

        // Menyaring data riwayat berdasarkan tahun pembayaran tertentu jika dipilih
        if ($request->filled('filter_tahun')) {
            $riwayatQuery->where('tahun', (int) $request->input('filter_tahun'));
        }

        // Mengambil data riwayat pembayaran lunas, diurutkan dari transaksi terbaru
        $daftarRiwayat = $riwayatQuery
            ->orderBy('tanggal_bayar', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Menghitung total akumulasi uang kas yang masuk dari data riwayat yang tampil
        $totalTerverifikasiLunas = $daftarRiwayat->sum('nominal');

        // Mengonversi angka bulan (1-12) menjadi nama teks bulan Bahasa Indonesia (contoh: "Oktober")
        $namaBulanBerjalan = IuranService::DAFTAR_BULAN[$bulanBerjalan] ?? 'Bulan ' . $bulanBerjalan;
        $periodeAktifTeks  = "{$namaBulanBerjalan} {$tahunBerjalan}";

        // Mengirimkan seluruh variabel yang telah diolah ke view blade 'admin.iuran'
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
     * Mencatat pelunasan tagihan iuran keluarga oleh Admin RT (Layar 2.2.18 / BR-06).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  \App\Services\IuranService  $iuranService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tandaiLunas(Request $request, int $id, IuranService $iuranService)
    {
        // 1. Mencari data tagihan iuran berdasarkan ID beserta relasi keluarganya
        $iuran = Iuran::with('keluarga')->findOrFail($id);

        // 2. Memvalidasi input dari form modal konfirmasi pelunasan
        $validated = $request->validate([
            'tanggal_bayar' => ['nullable', 'date'],             // Tanggal warga menyetorkan uang iuran
            'nominal'       => ['nullable', 'numeric', 'min:0'],  // Nominal riil yang disetor (boleh custom jika ada penyesuaian)
            'catatan'       => ['nullable', 'string', 'max:500'], // Catatan tambahan bukti setor atau keterangan bayar
        ]);

        // 3. Menyiapkan nominal: jika diisi user maka pakai angka tersebut, jika kosong pakai nilai default service
        $nominal = isset($validated['nominal']) 
            ? (float) $validated['nominal'] 
            : null;

        // 4. Memproses pencatatan status LUNAS melalui IuranService
        $iuranService->catatPelunasan(
            $iuran,
            $validated['tanggal_bayar'] ?? null,
            $nominal,
            $validated['catatan'] ?? null
        );

        // 5. Menyusun pesan notifikasi sukses dengan rincian No. KK dan nama periode bulan yang dilunasi
        $bulanNama = IuranService::DAFTAR_BULAN[$iuran->bulan] ?? 'Bulan ' . $iuran->bulan;
        $noKk = $iuran->keluarga?->no_kk ?? '-';

        // 6. Mengembalikan halaman sebelumnya dengan pesan flash notifikasi hijau
        return redirect()->back()->with('success', "Pembayaran iuran KK No. {$noKk} periode {$bulanNama} {$iuran->tahun} berhasil dicatat LUNAS.");
    }

    /**
     * Memperbarui besaran tarif standar iuran bulanan RT untuk seluruh warga (Layar 2.2.17 / BR-06).
     *
     * Sesuai aturan bisnis BR-06: Perubahan tarif hanya berlaku untuk tagihan yang 'belum_lunas'
     * mulai bulan berjalan hingga akhir tahun. Tagihan yang sudah lunas atau bulan lampau tidak diubah.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\IuranService  $iuranService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function aturStandarIuran(Request $request, IuranService $iuranService)
    {
        // 1. Memvalidasi input tarif: wajib diisi, minimal Rp 1.000, catatan maksimal 500 karakter
        $validated = $request->validate([
            'nominal' => ['required', 'numeric', 'min:1000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        // 2. Menentukan waktu patokan mulai berlakunya tarif baru (bulan dan tahun saat ini)
        $now           = Carbon::now('Asia/Jakarta');
        $tahunBerjalan = $now->year;
        $bulanBerjalan = $now->month;
        $nominalBaru   = (float) $validated['nominal'];
        $catatanBaru   = isset($validated['catatan']) ? trim($validated['catatan']) : null;

        // 3. Menerapkan tarif baru via IuranService (mengembalikan jumlah baris data yang terupdate)
        $affected = $iuranService->aturStandarIuran($nominalBaru, $catatanBaru, $tahunBerjalan, $bulanBerjalan);

        // 4. Menyiapkan teks rupiah dan nama bulan untuk pesan konfirmasi
        $namaBulan = IuranService::DAFTAR_BULAN[$bulanBerjalan] ?? 'Bulan ' . $bulanBerjalan;
        $nominalFmt = 'Rp ' . number_format($nominalBaru, 0, ',', '.');

        // 5. Mengembalikan halaman dengan pesan sukses menginfokan periode dan jumlah tagihan yang disesuaikan
        return redirect()->back()->with('success', "Standar iuran bulanan berhasil diperbarui menjadi {$nominalFmt} / KK mulai periode {$namaBulan} {$tahunBerjalan} hingga akhir tahun ({$affected} baris diperbarui).");
    }

    /**
     * Helper fungsi private untuk menerapkan query pencarian teks (No. KK, Nama Warga Hidup, atau Alamat).
     *
     * Memastikan pencarian nama hanya mencocokkan anggota keluarga yang masih hidup (BR-13).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $keyword
     * @return void
     */
    private function terapkanFilterPencarian(Builder $query, string $keyword): void
    {
        // Membersihkan spasi di awal dan akhir kata kunci
        $q = trim($keyword);
        if ($q === '') {
            return;
        }

        // Mencari pada relasi tabel Keluarga:
        // 1. Cocok dengan No. KK
        // 2. ATAU cocok dengan Nama warga yang masih HIDUP
        // 3. ATAU cocok dengan Alamat rumah warga yang masih HIDUP
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
