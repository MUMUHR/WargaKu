# 📜 01. Kronologi Percakapan Lengkap (Step 0 s/d Step 13757+)

Dokumen ini merangkum seluruh percakapan pengguna (*User Requests*) dan respons asisten AI yang terjadi sepanjang pengembangan proyek WargaKu.

---

### Fase 1: Inisiasi Proyek, Desain UI, & Frontend Slicing (Step 0 – 3500)
1. **Permintaan Awal:** Membangun aplikasi web "WargaKu" untuk tata kelola administrasi RT 04 berpedoman pada PRD v2.8 dan DBML 7 tabel.
2. **Slicing 42 Desain Layar:**
   * **Area Publik:** Landing Page (hero section `bg_hero.png`, statistik kependudukan, pengumuman publish), detail pengumuman, form login, form registrasi KK.
   * **Area Warga:** Beranda Warga, Data Keluarga (tab anggota & riwayat pengajuan), form tambah anggota, form edit data warga, pengajuan surat pengantar, dan rekap 12 card iuran bulanan.
   * **Area Admin RT:** Dashboard admin, kelola pengumuman (CRUD + thumbnail), kelola data warga (tab KK & verifikasi pengajuan), detail KK, proses surat pengantar ber-TTD RT, dan kelola iuran RT.
3. **Teknologi Styling:** Murni **CSS Vanilla** dengan Custom Properties (`tokens.css`, `base.css`, `components.css`) dan icon Boxicons.

---

### Fase 2: Responsivitas Mobile, Format Surat Resmi, & Modal UI (Step 3500 – 5000)
1. **Responsivitas Ponsel:**
   * Form pengajuan perubahan data diatur agar data lama dan data baru tersusun rapi atas-bawah (*stacked*).
   * Tombol tab navigasi dioptimalkan agar mudah ditekan di layar sentuh.
2. **Standarisasi Surat Resmi RT:**
   * Kertas ukuran standar cetak **A4**.
   * Margin resmi tata usaha: **Atas 2.5 cm, Bawah 2.5 cm, Kiri 3.0 cm, Kanan 2.5 cm**. Spasi baris `line-height: 1.5`.
   * **Kop RW Dihapus:** Lingkup aplikasi difokuskan mutlak pada level Rukun Tetangga (RT 04).
   * Tanda tangan dan stempel digital RT hanya muncul pada surat yang telah berstatus `disetujui`.
3. **Modal UI:**
   * Modal detail biodata kependudukan warga.
   * Modal komparasi perubahan data *side-by-side* (data lama vs data baru).

---

### Fase 3: Migrasi Backend Laravel 12 & Penyesuaian Breeze (Step 5000 – 8500)
1. **Skema Bersih 7 Tabel InnoDB:**
   `users`, `keluarga`, `warga`, `riwayat_perubahan`, `surat`, `iuran`, `pengumuman`.
   * Seluruh relasi ke keluarga memakai `keluarga_id` (PK `keluarga.id`), bukan nomor KK.
2. **Kustomisasi Laravel Breeze:**
   * Login memakai `username` (NIK untuk warga, `adminrt` untuk pengurus RT).
   * Registrasi KK baru tanpa input password (password awal otomatis diset sama dengan NIK pendaftar).
   * Middleware proteksi peran: `admin` dan `warga`.

---

### Fase 4: Penegakan Aturan Bisnis Inti (BR-01 s/d BR-15) (Step 8500 – 13000)
1. **BR-01 & BR-04:** Satu akun bersama per KK, password awal & reset ke NIK pemilik akun aktif.
2. **BR-02 & BR-03:** Transfer akun otomatis jika pemilik akun meninggal/pindah (hierarki pewaris: Pasangan &rarr; Anak &ge;17 thn &rarr; Anggota Tertua). Password tidak berubah. Pembersihan akun jika seluruh anggota KK habis.
3. **BR-05:** Penomoran surat resmi sekuensial format Romawi `NNN/SP/RT04/BULAN_ROMAWI/TAHUN`, reset 001 tiap 1 Januari.
4. **BR-06:** *Lazy Generation* tagihan iuran 12 bulan saat menu dibuka (tanpa cron job). Bulan sebelum KK terdaftar berstatus N/A (tanpa baris di database).
5. **BR-08, BR-09, & BR-14:** Berkas bukti privat, validasi registrasi ketat, dan mutasi pindah KK tanpa membuat duplikasi baris baru di database.

---

### Fase 5: Penjelasan Kode, Perbaikan Bug SHDK, & Panduan PDF (Step 13000 – 13670)
1. **Penjelasan Standar Penamaan File:**
   * Format `'bukti_' . time() . '_' . uniqid() . '.' . $extension` dijelaskan sebagai standar industri programmer untuk menjamin keunikan nama berkas dan keamanan.
2. **Penjelasan Avatar Inisial Dua Huruf:**
   * Kode `$avatar = strtoupper(substr($words[0] ?? 'W', 0, 1) . substr($words[1] ?? ($words[0] ?? 'G'), 0, 1));` dijelaskan fungsinya untuk membuat avatar singkatan nama otomatis (misal: "Budi Santoso" &rarr; "BS").
3. **Perbaikan Bug Status Hubungan Keluarga (SHDK) Anak:**
   * Status hubungan anak yang sempat hilang saat edit diselaraskan murni ke nilai enum database (`anak`).
4. **Penerbitan Buku Panduan Resmi PDF:**
   * Dibuat dokumen resmi [`Panduan_Lengkap_WargaKu.pdf`](file:///d:/PROJECT_PBL/WargaKu/Panduan_Lengkap_WargaKu.pdf) (~721 KB) lengkap dengan ERD, 5 workflow bisnis, dan 10 FAQ sidang PBL. Script sementara `generate_pdf.py` telah dibersihkan.

---

### Fase 6: Refaktorisasi Arsitektur Service Layer & Audit Final (Step 13670 – 13757+)
1. **Diskusi Kritis Mutasi Pindah KK & Transfer Akun:**
   * Dijelaskan bahwa logika transfer akun dan pindah KK sebenarnya sangat ringkas di backend (~30 baris kode) dan menjadi nilai jual utama (A+) saat sidang PBL.
2. **Refaktorisasi ke Service Layer (Opsi 1):**
   * Controller yang sebelumnya membengkak hingga 634 baris dipecah ke 2 service:
     * `app/Services/KependudukanService.php`
     * `app/Services/PengajuanPerubahanService.php`
   * `Admin\WargaController.php` terpangkas ke ~315 baris (Skinny Controller) dengan Constructor Dependency Injection.
   * `Warga\KeluargaController.php` mendelegasikan seluruh alur permohonan ke service.
3. **Audit Pengujian:**
   * **103 tes otomatis (464 assertions) lulus 100% tanpa ada error.**

---

### Fase 7: Wacana & Analisis Penyederhanaan Fitur (Step 13757+)
1. **Wacana Penyederhanaan Perubahan Data:**
   * Muncul ide kelompok untuk menghapus fitur warga mengajukan "Ubah Biodata Warga". Warga hanya bisa mengajukan "Tambah Anggota Baru", sedangkan perbaikan data/typo dilakukan via lapor ke Admin RT (misal lewat WhatsApp) dan Admin mengedit langsung.
   * Manfaat: Menghapus logika diff engine komparasi 10 kolom JSON dan menyederhanakan modal review admin. Status: Wacana diskusi internal kelompok.
2. **Analisis Transfer Akun & Pindah KK:**
   * Keduanya terbukti sangat ringan, tidak membebani UI, dan direkomendasikan untuk tetap dipertahankan.
