# ⚖️ 03. Aturan Bisnis Inti (Business Rules BR-01 s/d BR-15)

Seluruh AI Assistant yang bekerja di repositori ini **wajib mematuhi 15 aturan bisnis berikut tanpa kompromi**.

---

| Kode | Nama Aturan | Implementasi Teknis & Ketentuan Mutlak |
| :--- | :--- | :--- |
| **BR-01** | **Akun KK Bersama** | Satu Kartu Keluarga hanya memiliki 1 akun login di tabel `users`. `users.username` adalah NIK pemilik akun aktif. Seluruh anggota KK berbagi `warga.user_id` yang sama. |
| **BR-02** | **Transfer Akun Otomatis** | Dipicu setiap kali status warga berubah keluar dari `hidup` (meninggal/pindah rumah/pindah KK). Jika dia pemilik akun, sistem otomatis mengalihkan `users.username` ke: 1) Kepala Keluarga, 2) Istri/Suami, 3) Anak tertua &ge;17 thn, 4) Anggota Tertua. **Password tidak berubah.** |
| **BR-03** | **Pembersihan Akun Otomatis** | Jika seluruh anggota dalam 1 KK keluar dari status `hidup` (KK habis), record di tabel `users` dihapus dalam transaksi database. KK non-aktif dan tidak ditagih iuran. |
| **BR-04** | **Password Default & Reset** | Password pendaftaran baru = NIK. Reset password oleh Admin RT = NIK pemilik akun aktif saat itu. **Tidak ada fitur reset password mandiri via email.** |
| **BR-05** | **Penomoran Surat Sekuensial** | Format: `NNN/SP/RT04/BULAN_ROMAWI/TAHUN`. Nomor urut `NNN` sekuensial per tahun kalender dan otomatis reset ke `001` setiap 1 Januari. Dibuat saat disetujui. |
| **BR-06** | **Lazy Generation Iuran 12 Bulan** | Tagihan 12 bulan di-generate saat menu iuran dibuka (tanpa cron job). KK terdaftar tengah tahun berstatus N/A (tanpa baris di DB). Nominal awal disalin dari baris iuran terbaru (fallback Rp 75.000). Pop-up standar hanya mengubah bulan berjalan s/d Desember untuk baris `belum_lunas`. |
| **BR-07** | **Kekekalan Surat Disetujui** | Surat yang telah berstatus `disetujui` tidak dapat dibatalkan oleh warga maupun dihapus oleh admin (arsip permanen RT). |
| **BR-08** | **Penyimpanan Berkas Privat** | Berkas bukti kependudukan (KTP/KK) disimpan di disk privat atau direktori terproteksi. Wajib dilampirkan saat mengajukan perubahan data. |
| **BR-09** | **Validasi Registrasi Ketat** | Menolak NIK yang sudah aktif, menolak NIK meninggal/pindah tanpa rekomendasi admin, menolak No KK yang sudah memiliki akun, dan mengizinkan NIK `pindah_kk` membentuk KK baru. |
| **BR-10** | **Tanpa Snapshot Surat** | Tabel `surat` tidak menyimpan salinan kolom nama/alamat. Seluruh biodata surat dirender secara *on-demand* melalui relasi Eloquent `warga_tujuan_id`. |
| **BR-11** | **Default Status Ekonomi Mampu** | Pendaftaran KK baru secara otomatis menetapkan `status_ekonomi = mampu` di tingkat aplikasi (karena kolom DB NOT NULL tanpa default). |
| **BR-12** | **Otorisasi & Keamanan Sesi** | Akses dibatasi peran (`admin` dan `warga`). Warga hanya boleh mengakses dan mengelola Kartu Keluarganya sendiri. |
| **BR-13** | **Surat Terbatas Anggota Sendiri** | Warga hanya dapat mengajukan surat pengantar untuk anggota keluarga yang terdaftar dalam KK-nya sendiri dan berstatus `hidup`. |
| **BR-14** | **Pindah KK Tanpa Duplikasi** | Anggota pindah KK diubah statusnya menjadi `pindah_kk`. Di KK tujuan, Admin memindahkan baris warga tersebut dengan memperbarui `keluarga_id` (tanpa membuat baris baru di database). |
| **BR-15** | **Publikasi Berita & Pengumuman** | Hanya pengumuman dengan status `publish` yang ditampilkan di landing page publik dan beranda warga. Status `draft` hanya terlihat oleh admin. |

---

## Aturan Integritas Referential Database
* `keluarga_id` pada tabel `warga`, `iuran`, dan `riwayat_perubahan` adalah **NOT NULL dengan `cascadeOnDelete`**.
* `user_id` pada tabel `warga` adalah **nullable dengan `nullOnDelete`** (saat akun KK dihapus di BR-03, riwayat kependudukan warga tetap utuh di database).
