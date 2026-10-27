# Kronologi Lengkap Seluruh Percakapan & Pengembangan Proyek "WargaKu"
**Proyek:** PBL-TRPL102 | Lingkup RT 04 | Laravel 12 + MySQL (InnoDB)  
**Dokumen Riwayat Historis:** Dari Awal (Step 0) Sampai Sekarang (Step 13757+)

Dokumen ini memetakan seluruh perjalanan, keputusan, perbaikan bug, refaktorisasi, dan tanya-jawab dari awal mula proyek WargaKu dirancang hingga tahap final saat ini.

---

## DAFTAR ISI KRONOLOGI
1. [Fase 1: Inisiasi Proyek, Desain UI, & Frontend Slicing](#fase-1-inisiasi-proyek-desain-ui--frontend-slicing)
2. [Fase 2: Responsivitas Mobile, Layout Surat Resmi, & Modal UI](#fase-2-responsivitas-mobile-layout-surat-resmi--modal-ui)
3. [Fase 3: Migrasi Backend Laravel 12 & Penyesuaian Breeze](#fase-3-migrasi-backend-laravel-12--penyesuaian-breeze)
4. [Fase 4: Penegakan Aturan Bisnis Inti (BR-01 s/d BR-15) & Audit Mutu](#fase-4-penegakan-aturan-bisnis-inti-br-01-sd-br-15--audit-mutu)
5. [Fase 5: Penjelasan Kode, Perbaikan Bug SHDK, & Pembuatan PDF Panduan](#fase-5-penjelasan-kode-perbaikan-bug-shdk--pembuatan-pdf-panduan)
6. [Fase 6: Refaktorisasi Arsitektur Service Layer & Audit Final](#fase-6-refaktorisasi-arsitektur-service-layer--audit-final)

---

## Fase 1: Inisiasi Proyek, Desain UI, & Frontend Slicing (Step 0 – 3500)

* **Tujuan & Permintaan Awal:**
  * Pengguna meminta pembangunan sistem informasi kependudukan RT bernama "WargaKu" berdasarkan file PRD (`PRD_WargaKu.md`) dan skema basis data (`DB_WAR.dbml`).
  * Desain antarmuka berpedoman pada 42 tangkapan layar desain di folder `ui-wargaku/` (LandingPage, Warga, AdminRT).
  * Arsitektur frontend disepakati menggunakan **CSS Vanilla murni** dengan *Design Tokens* (`tokens.css`, `base.css`, `components.css`) dan icon Boxicons, tanpa framework CSS utilitas seperti Tailwind atau Bootstrap.

* **Fitur & Layar yang Dibangun:**
  1. **Landing Page Publik:**
     * Hero section dengan background `bg_hero.png` dan kartu statistik kependudukan RT 04.
     * Daftar berita/pengumuman terkini yang hanya berstatus `publish`.
     * Halaman detail pengumuman dan navigasi publik.
  2. **Autentikasi (Prototipe Awal):**
     * Form Login dengan pemisahan peran Warga dan Admin RT.
     * Form Registrasi Kartu Keluarga baru (input data kepala keluarga, NIK, No. KK 16 digit).
  3. **Portal Area Warga:**
     * Beranda Warga dengan ringkasan status keluarga dan pengumuman RT.
     * Halaman Data Keluarga: Tab 1 (Daftar Anggota Keluarga) dan Tab 2 (Riwayat Pengajuan Perubahan Data).
     * Form Pengajuan Perubahan Data Anggota dan Form Pendaftaran Anggota Keluarga Baru.
     * Halaman Layanan Pengajuan Surat Pengantar RT.
     * Halaman Rekap Iuran Bulanan Warga (grid 12 kartu bulan).
     * Profil & Keamanan Warga (kontak & ganti kata sandi).
  4. **Portal Area Admin RT:**
     * Dashboard Admin dengan statistik jumlah KK, warga hidup, gender, dan antrean verifikasi.
     * Kelola Pengumuman: tabel data, form tambah/edit berita dengan thumbnail gambar, dan aksi hapus.
     * Kelola Data Warga & Verifikasi: Tab 1 (Daftar Seluruh KK) dan Tab 2 (Antrean Verifikasi Perubahan Data).
     * Halaman Detail KK: rincian biodata anggota keluarga, status ekonomi, status tinggal, dan tombol aksi reset password.
     * Proses Pengajuan Surat: daftar permohonan surat, preview surat pengantar berstempel RT, persetujuan dan penolakan.
     * Kelola Iuran Warga: tabel status iuran 12 bulan per KK, pencatatan pelunasan, dan pengaturan nominal tarif bulanan.

---

## Fase 2: Responsivitas Mobile, Layout Surat Resmi, & Modal UI (Step 3500 – 5000)

* **Penyempurnaan Responsivitas Layar Ponsel:**
  * Penataan ulang form pengajuan perubahan data di layar sempit agar data lama dan data baru tersusun atas-bawah (*stacked layout*) yang rapi dan tidak meluap ke luar layar.
  * Perbaikan tombol tab navigasi agar mudah ditekan di layar sentuh (*touch target* yang memadai).

* **Standarisasi Surat Pengantar Resmi:**
  * Penyesuaian ukuran kertas ke format standar cetak **A4**.
  * Margin resmi tata usaha persuratan: **Atas 2.5 cm, Bawah 2.5 cm, Kiri 3.0 cm, Kanan 2.5 cm**.
  * Pengaturan spasi baris (*line-height: 1.5*) dan penempatan area tanda tangan serta cap stempel RT di bagian bawah.
  * **Penghapusan Bagian RW:** Sesuai arahan pengguna, lingkup aplikasi difokuskan mutlak pada level Rukun Tetangga (RT 04), sehingga elemen RW dihapus.
  * **Kekekalan Surat & Tanda Tangan:** Tanda tangan dan stempel RT hanya muncul jika surat telah berstatus `disetujui`.

* **Penyempurnaan Modal UI:**
  * Pembuatan modal detail kependudukan warga.
  * Pembuatan modal review komparasi perubahan data dari sudut pandang Warga dan Admin (menampilkan komparasi *side-by-side* atribut yang diubah, lampiran berkas asli seperti KTP/Akta, dan catatan alasan verifikasi RT).

---

## Fase 3: Migrasi Backend Laravel 12 & Penyesuaian Breeze (Step 5000 – 8500)

* **Migrasi Database & Skema Bersih 7 Tabel:**
  * Migrasi database InnoDB sesuai `DB_WAR.dbml`:
    1. `users` (id, username, password, no_telp, role)
    2. `keluarga` (id, no_kk, status_ekonomi, status_tinggal)
    3. `warga` (id, user_id, keluarga_id, nik, nama, tempat/tgl lahir, jk, agama, pendidikan, pekerjaan, alamat, status_hubungan, status_warga)
    4. `riwayat_perubahan` (id, keluarga_id, warga_id, jenis_pengajuan, data_lama, data_baru, bukti_path, status, alasan, admin_id)
    5. `surat` (id, pemohon_id, warga_tujuan_id, keperluan, nomor_surat, status, alasan)
    6. `iuran` (id, keluarga_id, nominal, catatan, bulan, tahun, status, tanggal_bayar)
    7. `pengumuman` (id, admin_id, judul, isi, gambar, status_publikasi)
  * Relasi foreign key menggunakan `keluarga_id` (INT auto-increment), bukan string nomor KK, demi integritas dan performa index.

* **Penyesuaian Laravel Breeze:**
  * Kustomisasi login: username menggunakan NIK (untuk warga) dan `adminrt` (untuk pengurus RT).
  * Pendaftaran akun KK baru: otomatis menginisialisasi entitas keluarga (`mampu`, `tetap`) dan anggota warga (`hidup`), tanpa input password manual (password awal otomatis diset sama dengan NIK pendaftar).
  * Pembuatan middleware proteksi role: `admin` dan `warga`.

---

## Fase 4: Penegakan Aturan Bisnis Inti (BR-01 s/d BR-15) & Audit Mutu (Step 8500 – 13000)

* **Implementasi Business Rules Mutlak:**
  * **BR-01 (Akun KK Bersama):** Satu KK hanya memiliki tepat 1 akun `users` yang digunakan bersama.
  * **BR-02 & BR-03 (Transfer Akun Otomatis & Pembersihan Akun):**
    Jika kepala keluarga/pemilik akun meninggal dunia atau pindah, sistem otomatis mewariskan username ke:
    1. Kepala Keluarga baru
    2. Istri atau Suami
    3. Anak tertua dewasa (&ge; 17 tahun)
    4. Anggota keluarga tertua  
    *Kata sandi tidak diubah agar keluarga tersisa tetap bisa login.* Jika seluruh anggota KK meninggal/pindah, akun dihapus dalam transaksi DB.
  * **BR-04 (Password Default & Reset):** Tidak ada lupa password mandiri via email. Reset dilakukan oleh Admin RT dengan 1 klik ke NIK pemilik akun aktif saat itu.
  * **BR-05 (Format Nomor Surat Sekuensial):** Format resmi `NNN/SP/RT04/BULAN_ROMAWI/TAHUN` (contoh: `007/SP/RT04/X/2026`), nomor urut otomatis kembali ke `001` setiap 1 Januari.
  * **BR-06 (Lazy Generation Iuran 12 Bulan):** Tagihan 12 bulan di-generate saat menu iuran dibuka pertama kali. KK terdaftar tengah tahun hanya ditagih sejak bulan terdaftar (bulan sebelumnya N/A tanpa baris di database).
  * **BR-07 (Kekekalan Surat):** Surat yang telah disetujui tidak dapat dibatalkan warga maupun dihapus admin.
  * **BR-08 (Penyimpanan Berkas Privat):** Berkas bukti warga disimpan di disk privat untuk perlindungan data pribadi.
  * **BR-09 (Validasi Registrasi Ketat):** Mencegah NIK hidup mendaftar ulang, menolak NIK almarhum, dan mendeteksi nomor KK yang sudah berakun.
  * **BR-10 (Tanpa Snapshot Surat):** Biodata surat dirender on-demand via relasi Eloquent `warga_tujuan_id`.
  * **BR-14 (Mutasi Pindah KK Tanpa Duplikasi):** Warga pindah KK diubah statusnya menjadi `pindah_kk`, lalu dialihkan ke KK baru oleh Admin RT dengan memperbarui `keluarga_id`.

---

## Fase 5: Penjelasan Kode, Perbaikan Bug SHDK, & Pembuatan PDF Panduan (Step 13000 – 13670)

1. **Penjelasan Standar Penamaan File Bukti:**
   * Diskusi mengenai format `'bukti_' . time() . '_' . uniqid() . '.' . $extension`.
   * Dijelaskan bahwa format ini adalah standar industri programmer untuk menjamin keunikan nama berkas dan keamanan sistem.

2. **Penjelasan Avatar Inisial Dua Huruf:**
   * Diskusi mengenai potongan kode `$avatar = strtoupper(substr($words[0] ?? 'W', 0, 1) . substr($words[1] ?? ($words[0] ?? 'G'), 0, 1));`.
   * Dijelaskan fungsinya untuk membuat avatar singkatan nama otomatis (misal "Budi Santoso" &rarr; "BS").

3. **Perbaikan Bug Status Hubungan Keluarga (SHDK) Anak:**
   * *Masalah:* Status hubungan anak hilang dari form edit dan tidak tampil di riwayat pengajuan.
   * *Solusi:* Kode disederhanakan murni menggunakan enum database (`anak`) tanpa pemisahan artifisial ("anak kandung"), sehingga sinkron 100% dengan database.

4. **Pembuatan Dokumen Panduan Lengkap PDF:**
   * Pengguna meminta buku panduan komprehensif berisi workflow, ERD, dan FAQ untuk bahan belajar kelompok.
   * Dibuat file PDF resmi berkualitas tinggi: `Panduan_Lengkap_WargaKu.pdf` (~721 KB) menggunakan rendering Microsoft Edge Headless.
   * Script perantara `generate_pdf.py` dibersihkan sesuai instruksi.

---

## Fase 6: Refaktorisasi Arsitektur Service Layer & Audit Final (Step 13670 – 13757+)

1. **Diskusi Kritis Mutasi Pindah KK & Transfer Akun:**
   * Pengguna bertanya: *"Menurutmu sistem status pindah KK dan transfer otomatis kalau meninggal ribet ga?"*
   * *Jawaban:* Di atas kertas terdengar rumit, tetapi di kodingan backend sangat ringkas (~35 baris kode `if-else` seleksi pewaris). Konsep ini merupakan **selling point bernilai A+** saat sidang PBL karena mendemonstrasikan penanganan siklus hidup data (*data lifecycle*) nyata.

2. **Keputusan Refaktorisasi Service Layer (Opsi 1):**
   * Pengguna mengamati controller warga dan admin yang membengkak hingga 600+ baris dan menyetujui pemisahan ke Service Layer.
   * Dipilih **Opsi 1 (Dua Service Modular)**:
     * 📁 `app/Services/KependudukanService.php` (Data KK & Warga, koreksi biodata, mutasi, BR-02/BR-03 transfer akun, BR-04 reset password, BR-14 pindah KK).
     * 📁 `app/Services/PengajuanPerubahanService.php` (Alur pengajuan 2-fase ubah data & tambah anggota, diff engine perbandingan snapshot, upload bukti privat, verifikasi persetujuan/penolakan transaksional).

3. **Hasil Refaktorisasi Controller:**
   * `Admin\WargaController.php` terpangkas dari **634 baris menjadi 315 baris**.
   * `Warga\KeluargaController.php` bersih dan ramping mendelegasikan seluruh permohonan ke service.
   * Kedua controller menerapkan *Constructor Dependency Injection*.

4. **Audit Pengujian & Verifikasi Kualitas:**
   * PHP Syntax Linting: 0 syntax error di seluruh service dan controller.
   * Route List: Seluruh 19 route terdaftar sempurna.
   * **Automated Test Suite: 103 passed (464 assertions) lolos 100% tanpa ada error atau regresi.**

5. **Jawaban Jujur Kompleksitas Modul:**
   * Pengguna bertanya jujur: *"Berarti yang sangat rumit dan panjang itu hanya pengajuan perubahan data keluarga ya?"*
   * *Jawaban:* **Ya, 100% benar.** Modul Pengajuan Perubahan Data Keluarga adalah modul paling rumit di WargaKu karena merupakan sistem approval 2-fase dengan diff snapshot JSON dan efek domino kependudukan. Modul lainnya (Pengumuman, Profil, Surat Pengantar, Iuran) jauh lebih sederhana dan lurus.

---

## Fase 7: Wacana & Analisis Penyederhanaan Fitur (Bahan Diskusi Internal Kelompok)

1. **Wacana Penyederhanaan Modul Perubahan Data:**
   * *Gagasan Pengguna:* Menghapus fitur warga mengajukan "Ubah Biodata Warga". Warga hanya bisa mengajukan "Pendaftaran Anggota Baru" (+ berkas bukti).
   * *Alur Alternatif:* Jika ada typo nama atau perubahan data warga lama, warga cukup menghubungi Admin RT (misal via WhatsApp atau tatap muka), lalu Admin RT yang melakukan koreksi langsung via menu *Edit Data Warga* (koreksi langsung oleh Admin sudah ada dan rapi).
   * *Manfaat Teknis:*
     * Menghapus seluruh logika *Diff Engine* komparasi 10 kolom JSON (`data_lama` vs `data_baru`).
     * Menghilangkan kerumitan modal UI perbandingan atribut hijau/merah.
     * Logika verifikasi Admin menjadi 1 jalur tunggal: insert anggota baru saat disetujui.
   * *Status:* Masih berupa wacana/perencanaan untuk didiskusikan bersama tim kelompok PBL.

2. **Analisis Logika "Transfer Akun Otomatis" & "Status Pindah KK":**
   * *Pertanyaan Pengguna:* Apakah logika transfer akun otomatis (saat meninggal/pindah) dan status pindah KK juga tergolong rumit?
   * *Kesimpulan Analisis:*
     * **Transfer Akun Otomatis:** **Sangat Ringan & Santai.** Hanya berjalan di background (~30 baris kode), tidak punya beban UI sama sekali, hanya mengubah 1 kolom (`users.username`), dan kata sandi tidak diubah. Menjadi nilai jual (selling point) A+ saat presentasi.
     * **Status Pindah KK:** **Sangat Sederhana.** Hanya mengubah status ke `pindah_kk` di KK lama, dan memperbarui `keluarga_id` ke KK baru saat didaftarkan ulang (hanya 1 baris update database). Sangat bersih dan mencegah duplikasi NIK.
   * *Rekomendasi:* Kedua fitur ini **sangat layak dipertahankan** karena kodenya ringkas, sudah lulus uji, dan tidak membebani pemahaman kelompok.

