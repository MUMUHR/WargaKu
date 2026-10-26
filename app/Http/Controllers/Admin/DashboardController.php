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
     * Menampilkan dashboard utama Admin RT dengan data statistik riil dari database.
     *
     * Method ini mengumpulkan seluruh metrik ringkasan yang dibutuhkan di halaman
     * beranda admin, seperti data kependudukan, antrean verifikasi pengajuan,
     * keuangan iuran bulan berjalan, dan pengumuman terbaru.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // 1. Mengambil total jumlah Kartu Keluarga (KK) yang terdaftar di RT 04
        $totalKk = Keluarga::count();

        // 2. Mengambil total warga yang berstatus masih hidup (warga meninggal tidak dihitung dalam populasi aktif)
        $totalWargaHidup = Warga::where('status_warga', 'hidup')->count();

        // 3. Menghitung pengajuan perubahan data keluarga dari warga yang statusnya masih 'menunggu' verifikasi
        $pengajuanDataMenunggu = Riwayat_Perubahan::where('status', 'menunggu')->count();

        // 4. Menghitung pengajuan surat pengantar dari warga yang statusnya masih 'menunggu' persetujuan admin
        $pengajuanSuratMenunggu = Surat::where('status', 'menunggu')->count();

        // 5. Menjumlahkan kedua jenis pengajuan di atas untuk ditampilkan sebagai total notifikasi badge antrean
        $totalPengajuanMenunggu = $pengajuanDataMenunggu + $pengajuanSuratMenunggu;

        // 6. Mengambil angka bulan saat ini (1 sampai 12) menggunakan helper waktu Carbon now()
        $bulanSekarang = (int) now()->format('n');

        // 7. Mengambil angka tahun 4 digit saat ini (contoh: 2026)
        $tahunSekarang = (int) now()->format('Y');

        // 8. Membuat teks nama bulan dan tahun terlokalisasi dalam Bahasa Indonesia (contoh: "Oktober 2026")
        $namaBulanTahun = now()->translatedFormat('F Y');

        // 9. Menghitung total nominal iuran yang terkumpul (status lunas) khusus pada bulan dan tahun berjalan
        $iuranTerkumpulBulanIni = Iuran::where('status', 'lunas')
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->sum('nominal');

        // 10. Mengambil 3 data pengumuman paling baru beserta relasi admin yang membuatnya (eager loading 'admin')
        $pengumumanTerbaru = Pengumuman::with('admin')
            ->latest() // Mengurutkan dari yang terbaru (ORDER BY created_at DESC)
            ->take(3)  // Membatasi hanya 3 data teratas (LIMIT 3)
            ->get();

        // 11. Menghitung total seluruh pengumuman yang pernah diterbitkan
        $totalPengumuman = Pengumuman::count();

        // 12. Mengirim seluruh data metrik di atas ke file view blade 'admin.beranda' menggunakan compact()
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
