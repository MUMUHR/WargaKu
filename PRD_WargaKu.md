# PRD — Aplikasi Informasi Warga "WargaKu"

| Item | Isi |
| --- | --- |
| Project | PBL-TRPL102, Program Studi Teknologi Rekayasa Perangkat Lunak, Politeknik Negeri Batam |
| Lingkungan | RT 04 / RW 08 |
| Versi PRD | 2.7 (2026-10-02) — menggantikan v2.6. Perubahan v2.7: kebijakan pembersihan Tailwind dibatalkan (setup Tailwind bawaan Breeze dibiarkan terpasang dan tidak dipakai), view login dan registrasi ditulis ulang mengikuti PNG, lokasi CSS vanilla dipindah ke `public/css/`. Perubahan v2.6: varian Breeze ditetapkan Blade + Alpine.js (O-01 selesai) dan aturan JavaScript ditambahkan; pengecualian login Admin RT pada BR-04; Bagian 12 disamakan dengan aturan 4 (teks antarmuka hanya Bahasa Indonesia). Dasar v2.5: sinkronisasi dengan laporan RPL-102 revisi 1, `DB_WAR.dbml`, dan diagram arsitektur; keputusan O-03, O-12, O-13, O-14, O-15 sudah dimasukkan, O-16 sebagian. Nama file PNG di Lampiran C dikoreksi sesuai tangkapan layar folder terbaru |
| Stack | Laravel 12 (sudah disetup), Laravel Breeze varian Blade + Alpine.js (sudah terpasang), view Blade murni (tanpa Livewire/Inertia), MySQL, CSS vanilla untuk seluruh tampilan (Tailwind bawaan Breeze dibiarkan terpasang dan tidak dipakai), JavaScript seperlunya |
| Strategi | **Frontend-First**: seluruh tampilan diselesaikan dan divisualisasikan dulu, baru backend |
| Acuan terkait | `DB_WAR.dbml` (skema resmi, 7 tabel, tidak boleh diubah), `Diagram_Arsitektur_Workflow_WargaKu.md`, laporan RPL-102 revisi 1, desain UI/UX tim (folder `ui-wargaku/`, Lampiran C). Urutan prioritas jika bertentangan: Bagian 0.1 |

**Prinsip dokumen.** Dokumen ini adalah sumber kebenaran utama. Tidak ada penambahan fitur, perubahan Use Case (UC-01 s.d. UC-12), atau perubahan kode fungsional (F001 s.d. F017). Hal yang belum ditetapkan ditulis "belum ditentukan" pada Bagian 15 dan tidak diisi dengan tebakan.

---

## 0. Aturan untuk AI Coding Agent (AntiGravity)

1. PRD ini, DBML, laporan, dan diagram adalah acuan. Jika dokumen saling bertentangan, ikuti urutan prioritas pada Bagian 0.1. Jika kode atau permintaan bertentangan dengan dokumen, ikuti dokumen dan laporkan pertentangannya.
2. Jangan menambah fitur, halaman, tabel, atau kolom di luar yang tertulis. Jangan mengubah nama F, UC, tabel, kolom, dan nilai enum.
3. Kerjakan **Frontend dulu** (Bagian 13). Jangan membuat migrasi, model, atau logika backend sebelum fase Frontend ditandai selesai oleh pemilik proyek.
4. Seluruh teks antarmuka berbahasa Indonesia.
5. Gunakan nilai enum persis seperti di DBML (huruf kecil, underscore). Label tampilan mengikuti Lampiran A.
6. PNG di folder `ui-wargaku/` (Lampiran C) adalah acuan visual wajib: tata letak, urutan elemen, teks label, warna, dan tipografi. Perilaku, aturan bisnis, dan data mengikuti PRD dan DBML. Jika PNG berbeda dari PRD pada label, alur, atau fitur, laporkan dan jangan memilih sendiri. Jika suatu layar di Bagian 9 belum punya PNG, bangun sesuai katalog layar dengan komponen yang sama dan tandai "menunggu desain". PNG yang tidak punya padanan di PRD (Lampiran C.4) jangan dibangun sebelum ada keputusan.
7. Jangan menyimpan kredensial atau data warga nyata dalam kode. Data contoh hanya dummy. Pengecualian: akun seeder `adminrt` (Bagian 8) yang ditetapkan laporan UC-02.
8. Tempatkan dokumen di direktori proyek, usulan: `docs/PRD_WargaKu.md`, `docs/Diagram_Arsitektur_Workflow_WargaKu.md`, `docs/DB_WAR.dbml`. Folder desain `ui-wargaku/` berada di root proyek (usulan; sesuaikan bila lokasinya berbeda).
9. Gunakan **view Blade murni** dan **CSS vanilla**. Dilarang memakai kelas utilitas Tailwind, framework CSS lain, Livewire, dan Inertia pada view yang ditulis di proyek ini. Setup Tailwind bawaan Breeze dibiarkan, jangan dihapus atau diubah. JavaScript boleh dipakai seperlunya (Alpine.js bawaan Breeze atau JS vanilla). Aturan lengkap: Lampiran C.5.
10. Sebelum membangun layar, buka PNG yang sesuai pada Lampiran C dan cocokkan nama file dengan isi folder yang sebenarnya (huruf besar/kecil dan spasi bisa berbeda dari tulisan di PRD).

### 0.1 Prioritas sumber dan pertentangan yang sudah diselesaikan

Urutan prioritas: (1) `DB_WAR.dbml` untuk struktur data, (2) laporan RPL-102 untuk UC, F, dan daftar layar 2.2.x, (3) PRD ini untuk business rules dan detail layar, (4) diagram arsitektur hanya sebagai ilustrasi alur. Khusus tampilan visual, PNG di `ui-wargaku/` adalah acuan (lihat aturan 6).

Diagram arsitektur saat ini berbeda dari DBML dan laporan pada hal berikut. Yang berlaku adalah kolom kanan.

| Hal | Diagram arsitektur | Yang berlaku |
| --- | --- | --- |
| Jumlah tabel | 8 tabel, termasuk `settings` | 7 tabel (DBML dan laporan). Tidak ada tabel `settings`; nilai awal nominal iuran di file konfigurasi (BR-06) |
| Nominal iuran | Dibaca dari `settings`, contoh 50000 | Rp 75.000 di file konfigurasi hanya untuk baris pertama; selanjutnya disalin dari baris terbaru di tabel `iuran` (BR-06). Komentar DBML sudah disesuaikan ke Rp 75.000 |
| Lazy generation iuran | Tagihan bulan berjalan saja | 12 bulan tahun berjalan, mulai bulan awal tagih KK (BR-06) |
| `keluarga.status_ekonomi` | default `mampu` di DB | Tanpa default di DB; aplikasi mengisi `mampu` (BR-11) |
| Penghapusan akun | `warga.user_id` di-NULL-kan oleh FK (`ON DELETE SET NULL`) | DBML tidak memakai `ON DELETE SET NULL`; aplikasi mengosongkan `user_id` lebih dulu dalam satu transaksi (BR-03) |
| Password saat registrasi | Warga memilih password sendiri (asumsi di diagram bagian 12) | Tidak ada input password; password awal = NIK (BR-04) |

---

## 1. Ringkasan Produk

**Deskripsi.** WargaKu adalah aplikasi web untuk komunikasi dan administrasi antara pengurus RT dan warga dalam satu RT. Empat fitur utama: pengumuman digital, pengelolaan iuran, pengelolaan data warga/keluarga, dan pengajuan surat daring. Tujuannya menggantikan proses manual yang kurang terdokumentasi.

**Pengguna.**
- **Warga.** Satu akun mewakili satu Kartu Keluarga (KK) dan dipakai bersama seluruh anggota KK.
- **Admin RT.** Pengurus RT, tidak dibedakan berdasarkan jabatan (Ketua, Sekretaris, Bendahara).
- **Pengunjung tanpa login.** Hanya dapat membuka landing page (pengumuman dan informasi layanan publik). Dalam diagram arsitektur diperlakukan sebagai Warga yang belum login.

**Sasaran.**
- Warga mengurus perubahan data, penambahan anggota, pindah KK, dan surat pengantar secara mandiri.
- Admin RT memverifikasi pengajuan, menerbitkan surat PDF bernomor, mengelola iuran, dan mengelola pengumuman dengan jejak data yang rapi.

**Di luar cakupan.** Segala hal di luar F001–F017.

---

## 2. Strategi Eksekusi Frontend-First

1. **Tahap Frontend.** Seluruh layar dibangun dari desain UI/UX memakai data dummy yang bentuknya identik dengan skema DBML. Semua alur dan status dapat dilihat dan diklik tanpa backend.
2. **Tahap Backend.** Setelah Frontend selesai dan disetujui, data dummy diganti data nyata: migrasi, model, validasi, otorisasi, dan business rules (Bagian 7).
3. **Kontrak data.** Bentuk data dummy harus sama dengan kolom tabel di Bagian 8 (nama field, tipe, nilai enum). Dengan begitu penggantian ke database nyata tidak mengubah tampilan.
4. **Navigasi lintas peran.** Karena autentikasi belum aktif pada tahap Frontend, sediakan cara sementara untuk melihat area Warga dan Admin (misalnya form login dummy yang mengarahkan berdasarkan username `adminrt` atau lainnya). Cara sementara ini dihapus pada tahap Backend.

---

## 3. Stack & Catatan Setup

- Laravel 12 dengan Breeze varian **Blade + Alpine.js** (sudah terpasang). Seluruh layar dirender sebagai view Blade murni (layout, komponen Blade, `@include`/`@yield`); tidak memakai Livewire maupun Inertia.
- JavaScript boleh dipakai seperlunya untuk interaksi (modal, tab, konfirmasi, pratinjau berkas). Gunakan Alpine.js bawaan Breeze atau JS vanilla di `resources/js/`, dimuat lewat Vite. Library JS tambahan (mis. SweetAlert2 untuk pop-up) hanya boleh dipakai jika tampilannya dapat disesuaikan dengan PNG desain (Lampiran C.5).
- Basis data MySQL; ID Laravel `id()` bertipe BIGINT UNSIGNED.
- Penyimpanan berkas: disk **privat** untuk `bukti_path`, disk **publik** untuk thumbnail pengumuman.
- Surat PDF dirender saat diunduh dari data snapshot.

**Penyesuaian Breeze yang diperlukan (konsekuensi langsung dari skema).** Tabel `users` hanya berisi `id`, `username`, `password`, `no_telp`, `role`, dan timestamp, sehingga bawaan Breeze berikut harus disesuaikan pada tahap Backend:
- Login memakai `username` (NIK atau `adminrt`), bukan email.
- Registrasi memakai alur UC-01 (NIK, No. KK, data identitas, no. telepon), bukan form nama/email. Tidak ada input password; password awal = NIK (BR-04).
- Tidak ada verifikasi email dan tidak ada lewat email; reset password dilakukan Admin RT.
- Halaman profil mengubah `no_telp` dan password saja (UC-03, F016).
- Setup Tailwind bawaan Breeze (paket `tailwindcss`, PostCSS, `tailwind.config.js`, `resources/css/app.css`, layout dan view bawaan Breeze) **dibiarkan apa adanya**; jangan dihapus, dibersihkan, atau dimigrasi, untuk menghindari konflik. Pekerjaan tampilan dibatasi pada dua hal: (1) menulis ulang view login dan registrasi (Blade) mengikuti `Halaman Login.png` dan `Halaman Register.png`, memakai username/NIK dan tanpa kolom email; (2) menulis seluruh layar lain sebagai view baru. Semua view yang ditulis di proyek ini distyling dengan CSS vanilla (Lampiran C.5), tanpa kelas utilitas Tailwind. Perubahan pada controller dan request autentikasi (login memakai username, bukan email) mengikuti UC-01 dan UC-02.

---

## 4. Peran, Hak Akses, dan Navigasi

### 4.1 Hak akses

| Fungsi | Warga | Admin RT |
| --- | --- | --- |
| Landing page dan pengumuman publish | Ya, tanpa login | Ya |
| Registrasi akun KK | Ya, tanpa login | Tidak |
| Login, logout, ubah no. telepon, ganti password | Ya | Ya |
| Lihat data keluarga sendiri | Ya | Tidak |
| Ajukan ubah data, tambah anggota, pindah/pecah KK + bukti | Ya | Tidak |
| Batalkan pengajuan (selama menunggu) | Ya | Tidak |
| Ajukan surat, lihat status, preview, unduh PDF | Ya | Tidak |
| Lihat status iuran KK sendiri (read-only) | Ya | Tidak |
| Kelola pengumuman | Tidak | Ya |
| Kelola data warga/keluarga, koreksi, status ekonomi | Tidak | Ya |
| Verifikasi pengajuan perubahan data | Tidak | Ya |
| Tambah anggota ke KK (NIK baru atau warga pindah_kk) | Tidak | Ya |
| Reset password akun KK | Tidak | Ya |
| Proses pengajuan surat (preview, setujui, tolak) | Tidak | Ya |
| Kelola iuran, pelunasan, nominal, riwayat | Tidak | Ya |

Warga hanya mengakses data KK miliknya. Warga tidak dapat mengubah NIK dan No. KK secara langsung.

### 4.2 Menu sidebar

- **Admin RT** (sesuai 2.2.4): Beranda, Kelola Pengumuman, Kelola Data Warga & Verifikasi, Proses Pengajuan Surat, Kelola & Rekap Iuran, Pengaturan. Bagian atas sidebar menampilkan ikon profil dan identitas admin.
- **Warga** (sesuai 2.2.20): Beranda, Data Keluarga, Ajukan Surat, Iuran Warga, Profil & Keamanan. Bagian atas sidebar menampilkan ikon profil dan identitas warga.

---

## 5. Kebutuhan Fungsional

| Kode | Kebutuhan | UC | Layar | Kriteria penerimaan ringkas |
| --- | --- | --- | --- | --- |
| F001 | Pendaftaran akun yang mewakili satu KK (NIK sebagai username), termasuk KK baru oleh warga berstatus pindah_kk | UC-01 | X1 | Akun dibuat sesuai BR-09; NIK aktif ditolak; masuk ke dashboard setelah berhasil |
| F002 | Autentikasi berdasarkan peran Warga dan Admin RT | UC-02 | 2.2.2 | Kredensial salah menampilkan pesan error; berhasil diarahkan ke dashboard sesuai peran; sesi timeout mengarah ke login |
| F003 | Pengumuman berstatus publish tampil di landing page tanpa login | UC-04 | 2.2.1, X2 | Hanya publish yang tampil; kosong menampilkan "belum ada pengumuman" |
| F004 | Admin RT: tambah, lihat, ubah, hapus, atur status publikasi pengumuman | UC-06 | 2.2.5, 2.2.6 | CRUD berfungsi; form tidak lengkap menampilkan error; thumbnail opsional |
| F005 | Simpan dan tampilkan data warga dan keluarga per No. KK | UC-07, UC-08 | 2.2.8, 2.2.21 | Daftar anggota hanya berstatus hidup (BR-13) |
| F006 | Pengajuan perubahan data, termasuk Pindah/Pecah KK, + bukti | UC-07, UC-11 | 2.2.24, 2.2.21 | Bukti wajib; disetujui pindah_kk, meninggal, atau pindah_rumah membuat warga tidak tampil di daftar anggota KK |
| F007 | Pengajuan tambah anggota keluarga + bukti | UC-07, UC-11 | 2.2.22, 2.2.21 | Bukti wajib; disetujui menambah anggota ke KK |
| F008 | Admin RT: tampil/koreksi data keluarga (termasuk status ekonomi), tab verifikasi, tambah anggota ke KK (NIK baru atau warga pindah_kk), reset password akun KK | UC-08, UC-11 | 2.2.7–2.2.12 | Sesuai BR-02, BR-04, BR-08, BR-14 |
| F009 | Admin RT mengelola iuran per No. KK: pelunasan (termasuk bulan mendatang dan tunggakan), nominal bulanan, tampil status ekonomi | UC-10 | 2.2.16–2.2.18 | Sesuai BR-06 |
| F010 | Riwayat iuran pada halaman kelola iuran | UC-10 | 2.2.16 (tab Riwayat) | Menampilkan seluruh baris iuran termasuk yang lunas |
| F011 | Pengajuan surat pengantar oleh Warga, tampil di Kelola Surat Admin | UC-09, UC-12 | 2.2.25, 2.2.13 | Tersimpan dengan status menunggu; muncul di daftar Admin |
| F012 | Tabel status iuran 12 bulan untuk KK milik Warga | UC-05 | 2.2.26 | 12 card Januari–Desember sesuai BR-06; bulan sebelum KK terdaftar tampil N/A |
| F013 | Halaman Preview Surat; Admin setujui atau tolak (alasan opsional) | UC-12 | X5, 2.2.14, 2.2.15 | Setujui menghasilkan nomor surat; tolak menyimpan alasan |
| F014 | Status pengajuan surat dan alasan bila ada, untuk Warga | UC-09 | 2.2.25 | Status dan alasan penolakan tampil di riwayat |
| F015 | PDF surat setelah disetujui; Warga preview dan unduh | UC-09, UC-12 | 2.2.25, X7 | PDF memuat nomor surat dan data snapshot, bertanda tangan RT|
| F016 | Ubah no. telepon, ganti password, logout untuk semua pengguna | UC-02, UC-03 | X3, X4 | Password lama salah, konfirmasi beda, atau tidak memenuhi ketentuan menampilkan error |
| F017 | Badge pembaruan status pengajuan (surat dan perubahan data) bagi Warga | UC-07, UC-09 | Sidebar, 2.2.19 | Badge muncul saat status berubah; lihat O-04 |

---

## 6. Use Case

Nomor dan nama mengikuti laporan. Skenario lengkap ada pada laporan bagian 1.3.3.

| UC | Nama | Aktor | Ringkasan alur utama | Alternatif penting |
| --- | --- | --- | --- | --- |
| UC-01 | Mendaftar Akun | Warga | Isi NIK pendaftar pertama, No. KK, identitas, no. telepon. Sistem menetapkan NIK sebagai username dan password awal (BR-04), membuat akun, masuk dashboard | Data tidak valid: error. NIK hidup: ditolak. NIK pindah_kk: boleh membentuk KK baru, data diperbarui ke No. KK baru dengan status hidup |
| UC-02 | Login/Autentikasi | Warga, Admin RT | Isi username dan password, sistem memvalidasi lalu arahkan ke dashboard sesuai peran. Logout menghapus sesi | Kredensial salah: error. Tidak aktif lama: sesi dihapus, ke login |
| UC-03 | Kelola Profil | Warga, Admin RT | Buka profil, ubah no. telepon, ganti password (lama, baru, konfirmasi) | Password lama salah, konfirmasi beda, atau tidak memenuhi ketentuan: error |
| UC-04 | Mengelola Pengumuman (konteks: melihat) | Pengunjung, Warga | Buka landing page, lihat daftar publish, buka detail | Tidak ada pengumuman: info kosong |
| UC-05 | Memantau Iuran | Warga | Ringkasan iuran di beranda, lalu 12 card bulan dengan status Lunas, Belum Lunas, atau N/A (BR-06) | Gagal memuat: pesan "Gagal memuat informasi iuran, silakan muat ulang halaman." |
| UC-06 | Mengelola Pengumuman | Admin RT | Menu Kelola Pengumuman, tabel, aksi Tambah/Edit/Hapus/Ubah Status, judul, isi, thumbnail opsional | Form tidak lengkap: error |
| UC-07 | Mengelola Data Keluarga | Warga | Profil Keluarga: tab 1 tabel anggota (No. KK, kepala keluarga, anggota), tab 2 riwayat perubahan. Edit atau Tambah Anggota + bukti foto, Kirim. Dapat membatalkan selama belum diproses | Ditolak: status dan alasan tampil, dapat ajukan ulang. Meninggal/Pindah/Pecah KK disetujui: tidak tampil di daftar KK ini |
| UC-08 | Mengelola Data Warga | Admin RT | Daftar keluarga per No. KK + jumlah anggota, filter/pilih, Lihat. Tambah Anggota dengan NIK + status hubungan (BR-14). Reset akun ke default (NIK) | NIK pindah_kk: tarik data lama, No. KK baru, status hidup. NIK sudah terdaftar di KK lain: ditolak (BR-14). No. KK tidak ada: info data tidak tersedia |
| UC-09 | Mengajukan Surat | Warga | Menu Ajukan Surat, pilih anggota keluarga untuk surat, isi keperluan, Ajukan, status menunggu | Batal selama menunggu. Ditolak: alasan tampil |
| UC-10 | Mengelola Iuran dan Riwayat | Admin RT | Menu Kelola Iuran dengan 2 tab (Kelola Status Iuran, Riwayat Pembayaran). Daftar per No. KK dengan label Status Ekonomi, 12 bulan per KK, cari/filter, tombol Pelunasan, atur nominal | KK tidak ditemukan: info data tidak tersedia |
| UC-11 | Verifikasi Data Warga | Admin RT | Tab Verifikasi Data Warga, pilih pengajuan, periksa bukti, Setujui, data diperbarui dan status disetujui | Bukti tidak valid: Tolak + alasan |
| UC-12 | Memproses Pengajuan Surat Pengantar | Admin RT | Kelola Surat, pilih pengajuan, Halaman Preview Surat, Setujui: nomor surat unik, status disetujui, PDF ber-TTD RT  | Tolak + alasan, status ditolak |

---

## 7. Business Rules Resmi

### BR-01 Akun KK
- Satu akun (`users`) mewakili satu KK. Seluruh anggota KK berbagi `warga.user_id` yang sama.
- `users.username` warga = NIK pemilik akun (pendaftar pertama); admin = `adminrt`.
- Pemilik akun adalah warga dengan `warga.nik = users.username`.
- KK sesi login ditentukan dari warga yang `user_id`-nya sama dengan akun.

### BR-02 Transfer Akun Otomatis
Dipicu setiap kali `status_warga` seorang warga berubah dari `hidup` menjadi `pindah_kk`, `meninggal`, atau `pindah_rumah`, baik lewat pengajuan yang disetujui (UC-11) maupun koreksi langsung Admin RT (layar 2.2.10). Ini business rule backend pada F008; tidak ada fitur atau UC baru.
1. Baca `user_id` warga tersebut lebih dulu (disebut `akun_lama`), lalu set `status_warga` baru dan `warga.user_id` menjadi NULL. Langkah 2 sampai 4 memakai `akun_lama`.
2. Cek pemilik: `akun_lama.username = warga.nik`. Jika bukan pemilik akun: selesai, akun KK tetap aktif.
3. Jika pemilik akun dan masih ada anggota berstatus `hidup`: pilih pemilik baru berurutan: Kepala Keluarga, Istri/Suami, Anak Tertua usia 17+, Anggota Tertua. `users.username` diperbarui ke NIK pemilik baru. Password tidak berubah.
4. Jika tidak ada anggota hidup tersisa: lihat BR-03.

### BR-03 Pembersihan Akun
Jika seluruh anggota KK habis, record `users` langsung dihapus dari basis data (tabel `users` hanya 5 kolom tanpa flag `is_active`). Karena FK `warga.user_id` tidak memakai `ON DELETE SET NULL`, aplikasi mengosongkan `warga.user_id` terlebih dahulu lalu menghapus `users` dalam satu transaksi.

### BR-04 Ketentuan Password Akun (Default NIK & Pengubahan Opsional)

1. Password Default:
    - Karena formulir pendaftaran akun baru hanya berisi data kependudukan tanpa input password, maka password default otomatis di-set sama dengan NIK pemilik akun (`users.username`).
    - Skenario Reset Password oleh Admin RT mengembalikan password akun KK ke NIK pemilik akun saat ini (`users.username`), yang bisa berbeda dari pendaftar awal setelah BR-02.
2. Login & Pengubahan Password:
    - Warga login menggunakan NIK sebagai username dan NIK sebagai password awal. Admin RT login menggunakan username `adminrt` dengan password awal `adminrt` (seeder, Bagian 8).
    - Tidak Ada Paksaan Ganti Password: Pengguna langsung diarahkan ke Dashboard Utama setelah berhasil login tanpa dipaksa masuk ke layar Force Change Password.   
    - Pengubahan password bersifat opsional dan dapat dilakukan kapan saja oleh pengguna melalui menu Pengaturan Profil / Akun.



### BR-05 Penomoran Surat
Format `NNN/SP/RT04/RW08/{bulan_romawi}/{tahun}`. `NNN` sekuensial dan reset otomatis setiap pergantian tahun kalender. `nomor_surat` unik dan diisi saat disetujui.

### BR-06 Iuran
- **Lazy generation 12 bulan.** Saat menu Iuran dibuka (oleh Warga maupun Admin RT), sistem memeriksa setiap KK aktif (minimal satu anggota `hidup`). Untuk KK yang belum punya baris iuran bulan tertentu di tahun berjalan, sistem menyisipkan baris `belum_lunas` dari bulan awal tagih sampai Desember. Unique `(no_kk, bulan, tahun)` membuat proses ini aman diulang dan otomatis mencakup KK yang terdaftar setelah pembuatan pertama di tahun itu. Tidak ada cron. "Bulan berjalan" memakai zona waktu Asia/Jakarta.
- **Bulan awal tagih.** Jika tahun `keluarga.created_at` lebih kecil dari tahun berjalan: Januari. Jika sama: bulan `keluarga.created_at`.
- **Bulan sebelum bulan awal tagih tidak memiliki baris.** UI menampilkannya sebagai card **N/A** (sub-teks "Dikecualikan"). Tidak ada nilai enum baru karena skema tidak boleh diubah; N/A, Berjalan, dan Menunggak adalah tampilan turunan, bukan data.
- **Sumber nominal baris baru.** Nominal disalin dari baris iuran terbaru di database (urutan `tahun` lalu `bulan` terbesar), dengan prioritas: (1) baris terbaru berstatus `belum_lunas`; (2) jika tidak ada, baris terbaru berstatus apa pun; (3) jika tabel `iuran` masih kosong, nilai awal Rp 75.000 dari file konfigurasi (contoh `config/wargaku.php`). Prioritas (1) dipakai karena baris `lunas` (terutama yang dibayar di muka) bisa masih bernominal lama. Tidak ada tabel `settings`.
- **Pop-up Standar Iuran (2.2.17).** Admin memasukkan nominal baru. Sistem melakukan `UPDATE` kolom `nominal` pada baris `belum_lunas` dengan (tahun, bulan) mulai bulan berjalan sampai akhir tahun berjalan. Baris `lunas` dan baris bulan lampau (tunggakan) tidak berubah. Karena baris berikutnya menyalin dari baris terbaru, KK baru dan tahun baru otomatis memakai nominal baru tanpa mengubah file konfigurasi (lihat O-15 untuk batasannya).
- **Pelunasan.** Hanya Admin RT yang mencatat pelunasan. Admin dapat melunasi baris bulan mana pun di tahun berjalan, termasuk bulan mendatang (bayar di muka) dan tunggakan bulan lampau. Status menjadi `lunas`, `tanggal_bayar` terisi dengan waktu pencatatan, dan baris tetap ada sebagai riwayat. Bulan N/A tidak memiliki baris sehingga tidak dapat dilunasi. Admin dapat mengubah `nominal` dan `catatan` per baris.
- **Warga hanya melihat (read-only).** Warga tidak melunasi lewat aplikasi; pembayaran dilakukan ke pengurus RT dan dicatat Admin (tanpa payment gateway).
- **Tampilan card Warga (2.2.26)** diturunkan dari data dan bulan berjalan:

| Kondisi | Tampilan card |
| --- | --- |
| Tidak ada baris (bulan sebelum bulan awal tagih) | N/A, sub-teks "Dikecualikan" (warna: O-16) |
| `status = lunas`, bulan apa pun | LUNAS, hijau |
| `belum_lunas`, bulan = bulan berjalan | BELUM LUNAS dengan penanda BERJALAN, merah, highlight |
| `belum_lunas`, bulan sebelum bulan berjalan | MENUNGGAK, merah gelap atau badge merah solid |
| `belum_lunas`, bulan setelah bulan berjalan | BELUM LUNAS, sub-teks "Belum masuk periode", abu-abu (muted) |

Mockup tim memakai Mei sebagai bulan berjalan dan Januari–April lunas; itu data contoh, bukan nilai tetap.

### BR-07 Status Pengajuan
- Status `menunggu`, `disetujui`, `ditolak`, `dibatalkan` untuk `pengajuan_perubahan_data` dan `pengajuan_surat`.
- Warga hanya dapat membatalkan saat `menunggu`.
- Surat `disetujui` tidak boleh dihapus atau dibatalkan.
- Penolakan menyimpan `alasan` (opsional untuk surat); Warga melihat alasan dan dapat mengajukan ulang.
- `admin_id` dicatat saat diproses.

### BR-08 Pengajuan Perubahan Data
- Bukti wajib, disimpan di disk privat.
- `jenis_pengajuan`: `ubah_data` atau `tambah_anggota`. Pindah/Pecah KK = `ubah_data` dengan `data_baru = {"status_warga":"pindah_kk"}`.
- `data_lama` menyimpan snapshot (null untuk `tambah_anggota`).
- Disetujui: `tambah_anggota` menyisipkan warga baru (`user_id` akun KK, status hidup); `ubah_data` menerapkan `data_baru`. Perubahan keluar dari `hidup` memicu BR-02.
- Ditolak: data warga tidak berubah.
- Tambah anggota oleh Warga: NIK diisi pada form (No. KK otomatis dari sesi). Jika NIK sudah ada di tabel `warga` dengan status apa pun, pengajuan ditolak validasi dengan arahan menghubungi Admin RT; warga `pindah_kk` ditambahkan oleh Admin RT (UC-08).
- Warga `pindah_kk` dapat didaftarkan lewat F001 (KK baru) atau ditambahkan Admin RT ke KK yang sudah ada (UC-08): sistem menarik data lama, menetapkan No. KK baru, status `hidup`, dan `user_id` akun KK tujuan. Aturan validasi lengkap pada BR-14.

### BR-09 Registrasi (F001 / UC-01)
- NIK belum ada: jika No. KK sudah punya akun, ditolak dan diarahkan ke Login atau pengajuan tambah anggota; jika belum, `keluarga` dibuat bila belum ada (`status_ekonomi = mampu`), lalu warga, akun, dan `user_id` dibuat.
- NIK ada, status `hidup`: ditolak, arahkan ke Login.
- NIK ada, status `meninggal` atau `pindah_rumah`: ditolak, hubungi Admin RT.
- NIK ada, status `pindah_kk`: jika No. KK tujuan baru, dibuat KK baru dan akun; jika No. KK sudah ada, arahkan meminta Admin RT.

### BR-10 Pengajuan Surat
- `pemohon_id` wajib, otomatis dari sesi login (warga pemilik akun, `nik` = `users.username`); tidak dipilih.
- `warga_tujuan_id` wajib diisi aplikasi: anggota hidup pada KK sesi login, dipilih dari dropdown (data auto-fill dan terkunci). Tidak ada mode isi manual.
- Data identitas disalin ke kolom snapshot (`nama_lengkap`, `tempat_tanggal_lahir`, `jenis_kelamin`, `agama`, `alamat`). PDF dirender dari snapshot saat diunduh.

### BR-11 Keluarga & Pengumuman
- `keluarga.status_ekonomi` diisi `mampu` oleh aplikasi saat keluarga dibuat (kolom NOT NULL tanpa default di DB), dikoreksi Admin RT; `status_tinggal` default `tetap`.
- Pengumuman: `status_publikasi` default `draft`; hanya `publish` tampil di landing page; `gambar` opsional (thumbnail).

### BR-12 Otorisasi dan Sesi
- Akses dibatasi peran (`role`). Warga hanya melihat dan mengajukan atas nama KK-nya.
- Sesi tidak aktif melewati batas waktu dihapus dan diarahkan ke login (durasi: O-05).

### BR-13 Daftar Anggota
Daftar anggota KK (Warga dan Admin) hanya menampilkan `status_warga = hidup`.

### BR-14 Tambah Anggota oleh Admin RT (UC-08, layar 2.2.12)
- Aksi langsung Admin RT, tidak melalui tabel `pengajuan_perubahan_data`. `warga.no_kk` diisi dari KK yang sedang dibuka; `status_warga = hidup`; `user_id` = akun KK tersebut.
- Form NIK baru: NIK, nama, jenis_kelamin, status_hubungan wajib (kolom NOT NULL di DBML); kolom lain opsional.
- Validasi NIK:
  1. NIK belum ada di tabel `warga`: simpan record `warga` baru pada KK tersebut.
  2. NIK ada dengan `status_warga = pindah_kk`: jalankan alur BR-08 (tarik data lama, No. KK baru, status `hidup`, `user_id` akun KK tujuan). Ini pengecualian dari poin 3 karena merupakan inti UC-08.
  3. NIK ada pada KK lain dengan status lain: tolak dengan pesan "NIK sudah terdaftar dalam Kartu Keluarga lain".
  4. NIK sudah ada pada KK yang sama: tolak dengan pesan "NIK sudah terdaftar pada Kartu Keluarga ini".

---

## 8. Model Data

7 tabel. Definisi lengkap dan resmi ada di `DB_WAR.dbml`; skema tidak boleh diubah atau dibuat ulang. Ringkasan:

| Tabel | Kolom | Catatan |
| --- | --- | --- |
| users | id, username (unik, 50), password (255), no_telp (20, null), role (admin/warga, default warga), timestamps | username = NIK atau adminrt |
| keluarga | no_kk (PK, 16), status_ekonomi (mampu/kurang_mampu, NOT NULL, tanpa default di DB), status_tinggal (tetap/kontrak/kos/menumpang, default tetap), timestamps | |
| warga | id, user_id (FK users, null), no_kk (FK), nik (unik, 16), nama (100), tempat_lahir (null), tanggal_lahir (null), jenis_kelamin, agama (null), pendidikan_terakhir (null), pekerjaan (null), alamat (null), status_hubungan, status_warga (default hidup), timestamps | Index: no_kk, status_warga |
| pengajuan_perubahan_data | id, warga_id (FK, null), no_kk (FK), jenis_pengajuan, data_lama (json, null), data_baru (json), bukti_path (255), status (default menunggu), alasan (null), admin_id (FK, null), timestamps | |
| pengajuan_surat | id, pemohon_id (FK warga), warga_tujuan_id (FK warga, null), nama_lengkap, tempat_tanggal_lahir, jenis_kelamin, agama, alamat, keperluan, nomor_surat (unik, null), status (default menunggu), alasan (null), admin_id (FK, null), timestamps | Kolom snapshot nullable |
| iuran | id, no_kk (FK), nominal (decimal 10,2), catatan (null), bulan (tinyint), tahun (smallint), status (default belum_lunas), tanggal_bayar (datetime, null), timestamps | Unik (no_kk, bulan, tahun) |
| pengumuman | id, judul (150), isi, gambar (255, null), status_publikasi (default draft), admin_id (FK), timestamps | |

**Relasi.** keluarga 1:N warga, iuran, pengajuan_perubahan_data. users 1:N warga (opsional), 1:N pengajuan_perubahan_data.admin_id, pengajuan_surat.admin_id, pengumuman.admin_id. warga 1:N pengajuan_perubahan_data (opsional), 1:N pengajuan_surat sebagai pemohon (wajib) dan sebagai tujuan (opsional).

**Seeder.** Akun `adminrt` (role admin), password awal `adminrt` sesuai laporan UC-02. Password dapat diganti lewat Pengaturan (F016).

---

## 9. Katalog Layar

Nomor 2.2.x mengikuti laporan. Layar X1–X7 dibutuhkan oleh UC/F tetapi belum punya subbab di 2.2. Rute adalah usulan. File PNG tiap layar ada di Lampiran C.

### 9.1 Publik

| ID | Layar | Rute (usulan) | Elemen utama |
| --- | --- | --- | --- |
| 2.2.1 | Landing Page | `/` | Pengantar aplikasi, daftar pengumuman publish (thumbnail opsional, judul, ringkasan), tautan Login dan Daftar, informasi layanan publik. State: kosong "belum ada pengumuman" |
| X2 | Detail Pengumuman | `/pengumuman/{id}` | Judul, tanggal, gambar opsional, isi |
| 2.2.2 | Login | `/login` | Username (NIK atau adminrt), password, tombol Masuk, pesan error kredensial salah. Peran terdeteksi otomatis |
| X1 | Registrasi | `/daftar` | NIK pendaftar pertama, No. KK, data identitas (kolom wajib: nama, jenis_kelamin, status_hubungan), no. telepon, tanpa input password (BR-04); pesan penolakan sesuai BR-09; data keluarga (status ekonomi diisi mampu oleh aplikasi, status tinggal) |

### 9.2 Warga

| ID | Layar | Rute (usulan) | Elemen utama |
| --- | --- | --- | --- |
| 2.2.19 | Beranda Warga | `/warga` | Ringkasan iuran keluarga, badge status pengajuan. State: gagal memuat iuran |
| 2.2.20 | Sidebar Warga | semua halaman Warga | Menu sesuai 4.2, ikon dan identitas profil, badge |
| 2.2.21 | Kelola Data Keluarga | `/warga/keluarga` | Tab 1: tabel anggota (No. KK, kepala keluarga, anggota hidup), tombol Edit per anggota, tombol Tambah Anggota Keluarga |
| 2.2.21 | Pemantauan status pengajuan data | `/warga/keluarga` tab 2 | Tabel riwayat perubahan: jenis, tanggal, status (badge), alasan penolakan, tombol Batalkan hanya saat menunggu |
| 2.2.22 | Form Tambah Anggota Baru | `/warga/keluarga/anggota/tambah` | Field data warga termasuk NIK (kolom wajib: nik, nama, jenis_kelamin, status_hubungan; No. KK otomatis dari sesi), upload bukti (wajib), Kirim |
| 2.2.24 | Form Pengajuan Perubahan Data | `/warga/keluarga/warga/{id}/ubah` | Field yang dapat diubah (NIK dan No. KK tidak tampil), opsi perubahan status warga (`meninggal`, `pindah_rumah`, `pindah_kk` untuk pindah/pecah KK), upload bukti (wajib), Kirim |
| 2.2.25 | Pengajuan Surat | `/warga/surat` | Form: dropdown anggota KK (auto-fill terkunci), keperluan, Ajukan. Riwayat: status badge, alasan penolakan, Batalkan (hanya menunggu), Preview dan Unduh PDF (hanya disetujui) |
| X7 | Preview Surat (Warga) | `/warga/surat/{id}/preview` | Tampilan surat setelah disetujui dan tombol Unduh PDF; hanya surat `disetujui` milik KK sendiri (F015) |
| 2.2.26 | Iuran Warga | `/warga/iuran` | Grid 12 card Januari–Desember tahun berjalan, read-only. Status tiap card sesuai tabel BR-06 (Lunas hijau, Berjalan merah highlight, Menunggak merah gelap, "Belum masuk periode" abu-abu, N/A "Dikecualikan") |
| X3 | Profil & Keamanan | `/warga/profil` | Ubah no. telepon, ganti password (lama, baru, konfirmasi), logout |

### 9.3 Admin RT

| ID | Layar | Rute (usulan) | Elemen utama |
| --- | --- | --- | --- |
| 2.2.3 | Beranda Admin | `/admin` | Isi beranda belum ditentukan (O-06) |
| 2.2.4 | Sidebar Admin | semua halaman Admin | Menu sesuai 4.2 |
| 2.2.5 | Kelola Pengumuman | `/admin/pengumuman` | Tabel, aksi Tambah/Edit/Hapus/Ubah Status Publikasi |
| 2.2.6 | Form Tambah/Edit Pengumuman | `/admin/pengumuman/create`, `/{id}/edit` | Judul, isi, thumbnail opsional, status draft/publish |
| 2.2.7 | Kelola Data Warga & Verifikasi | `/admin/data-warga` | Tab Daftar Keluarga (No. KK, jumlah anggota, filter, Lihat) dan Tab Verifikasi Data Warga (daftar pengajuan menunggu) |
| X6 | Detail Verifikasi | `/admin/verifikasi/{id}` | Perbandingan data_lama dan data_baru, tampilan bukti, Setujui, Tolak + alasan |
| 2.2.8 | Detail Kartu Keluarga | `/admin/data-warga/{no_kk}` | Alamat, telepon, status ekonomi, anggota (nama, hubungan, JK, agama, pendidikan, pekerjaan, status warga), aksi. Alamat diambil dari `warga.alamat` milik anggota berstatus `kepala_keluarga` dan `hidup` pada No. KK tersebut; tampil "-" bila kosong. Telepon diambil dari `users.no_telp` akun KK |
| 2.2.9 | Reset Password Akun | modal di 2.2.8 | Konfirmasi reset ke default (NIK) |
| 2.2.10 | Edit Data Warga | `/admin/warga/{id}/edit` | Form koreksi data kependudukan, status_warga |
| 2.2.11 | Pop-up Detail Biodata | modal | Biodata lengkap warga |
| 2.2.12 | Tambah Anggota Keluarga (Admin) | `/admin/data-warga/{no_kk}/anggota/tambah` | Input NIK, status hubungan, dan biodata singkat bila NIK baru; validasi dan pesan error sesuai BR-14; perilaku pindah_kk sesuai BR-08 |
| 2.2.13 | Proses Pengajuan Surat | `/admin/surat` | Daftar pengajuan surat dengan status, pilih untuk preview |
| X5 | Halaman Preview Surat | `/admin/surat/{id}/preview` | Tampilan surat dari data pengajuan, tombol Setujui (membuka pop-up 2.2.14) dan Tolak (membuka pop-up 2.2.15), sesuai urutan UC-12 |
| 2.2.14 | Pop-up Persetujuan & Penerbitan Surat | modal | Konfirmasi persetujuan, nomor surat |
| 2.2.15 | Pop-up Penolakan & Alasan | modal | Input alasan (opsional untuk surat, diisi untuk verifikasi data) |
| 2.2.16 | Kelola & Rekap Iuran | `/admin/iuran` | Tab Kelola Status Iuran (tabel per No. KK, label Status Ekonomi, cari/filter, 12 bulan Januari–Desember tahun berjalan per KK dengan aksi Pelunasan pada bulan yang punya baris, termasuk bulan mendatang dan tunggakan, atur nominal) dan Tab Riwayat Pembayaran |
| 2.2.17 | Pop-up Standar Iuran Bulanan | modal | Input nominal baru; `UPDATE` baris `belum_lunas` dari bulan berjalan sampai Desember; baris baru berikutnya menyalin dari baris terbaru (BR-06) |
| 2.2.18 | Pop-up Konfirmasi Pembayaran | modal | Konfirmasi status menjadi lunas, termasuk untuk bulan mendatang dan tunggakan |
| X4 | Pengaturan/Profil Admin | `/admin/pengaturan` | Ubah no. telepon, ganti password, logout (F016, UC-03; Admin RT termasuk aktor UC-03) |

---

## 10. Pola Antarmuka Umum

**Status badge (semantik; warna mengikuti desain UI/UX).**
- Pengajuan: `menunggu`, `disetujui`, `ditolak`, `dibatalkan`.
- Iuran: `lunas`, `belum_lunas`; tampilan turunan (bukan nilai enum): N/A, Berjalan, Menunggak, Belum masuk periode.
- Pengumuman: `draft`, `publish`.
- Warga: `hidup`, `meninggal`, `pindah_rumah`, `pindah_kk`.
- Status ekonomi: `mampu`, `kurang_mampu`.

**State yang wajib ada pada tiap layar daftar atau data.** Normal, kosong, error muat, dan (untuk form) error validasi per field. Pesan baku yang sudah ditentukan: "belum ada pengumuman", "Gagal memuat informasi iuran, silakan muat ulang halaman.", "data tidak tersedia" untuk pencarian No. KK yang tidak ditemukan.

**Aturan aksi.**
- Tombol Batalkan hanya muncul saat status `menunggu`.
- Surat `disetujui`: tidak ada tombol Hapus atau Batalkan; muncul tombol Preview dan Unduh.
- Pengajuan `ditolak`: menampilkan alasan dan jalan untuk mengajukan ulang.
- Form bukti: field upload wajib dan tidak dapat dikirim tanpa berkas.
- Aksi destruktif (hapus pengumuman, reset password, persetujuan) memakai konfirmasi.

**Dummy data minimum untuk tahap Frontend** (agar semua state terlihat):
- 6 keluarga, campuran `mampu` dan `kurang_mampu`, status tinggal beragam.
- Warga berstatus hidup, meninggal, pindah_rumah, dan pindah_kk; satu KK dengan pemilik akun dan anggota lain.
- Pengajuan perubahan data dan pengajuan surat masing-masing berstatus menunggu, disetujui, ditolak, dibatalkan (surat disetujui memiliki nomor).
- Iuran 12 bulan: satu KK dengan bulan lampau campuran lunas dan belum_lunas (tampil Menunggak), bulan berjalan belum_lunas, satu bulan mendatang sudah lunas (bayar di muka), sisanya belum_lunas; satu KK terdaftar tengah tahun (bulan sebelumnya N/A); nominal berbeda pada satu baris.
- Pengumuman draft dan publish, dengan dan tanpa gambar; satu kondisi kosong.
- Akun dummy: satu admin dan satu warga.

---

## 11. Rute dan Pengelompokan (usulan)

- Publik: `/`, `/pengumuman/{id}`, `/login`, `/daftar`, `POST /logout`.
- Warga (`/warga/*`, wajib login dan role warga): beranda, keluarga, surat, iuran, profil.
- Admin (`/admin/*`, wajib login dan role admin): beranda, pengumuman, data-warga, verifikasi, surat, iuran, pengaturan.
- Berkas bukti dan PDF surat diakses lewat rute berotorisasi, bukan URL publik langsung.

---

## 12. Kebutuhan Non-Fungsional

| Kriteria | Parameter (sesuai laporan) |
| --- | --- |
| Availability | Berjalan 24 jam melalui web dan MySQL, kecuali perawatan atau pembaruan |
| Ergonomy | Antarmuka sederhana dan mudah digunakan semua peran |
| Bahasa | Bahasa Indonesia. Seluruh teks antarmuka berbahasa Indonesia (Bagian 0 aturan 4); opsi Bahasa Inggris pada laporan tidak dibangun |
| Safety | HTTPS dengan sertifikat SSL valid |
| Confidentiality | Autentikasi dan hak akses sesuai peran; warga hanya melihat data keluarganya |
| Data Integrity | Transaksi iuran dan pengajuan tercatat rapi, tanpa data hilang atau duplikat |

Konsekuensi teknis: persetujuan pengajuan dan transfer akun dijalankan dalam satu transaksi database; constraint unik pada `nik`, `username`, `nomor_surat`, dan `(no_kk, bulan, tahun)`; berkas bukti di disk privat. Target performa dan kapasitas belum ditentukan.

---

## 13. Rencana Implementasi

### Tahap Frontend

| Fase | Isi | Layar |
| --- | --- | --- |
| F0 Fondasi | Layout (publik, warga, admin), token (CSS custom properties diambil dari PNG) dan komponen dasar dengan Blade dan CSS vanilla (Lampiran C.5), sidebar, badge, tabel, tab, modal (JS seperlunya), form, lapisan data dummy, navigasi sementara lintas peran | Semua |
| F1 Publik & Auth | Landing, detail pengumuman, login, registrasi (semua kondisi penolakan BR-09) | 2.2.1, 2.2.2, X1, X2 |
| F2 Area Warga | Beranda, sidebar, data keluarga (2 tab), form tambah anggota, form ubah data, ajukan surat + riwayat, iuran, profil | 2.2.19–2.2.26, X3, X7 |
| F3 Area Admin | Beranda, pengumuman, data warga dan verifikasi, detail KK, edit, tambah anggota, reset password, surat dan preview, iuran dan pop-up, pengaturan | 2.2.3–2.2.18, X4–X6 |
| F4 Penyelesaian | Semua state (kosong, error, validasi), responsif, kesesuaian dengan desain, persetujuan pemilik proyek | Semua |

**Definition of Done Frontend.** Semua layar di Bagian 9 dapat dibuka dan dinavigasi; semua state di Bagian 10 dapat dilihat dengan dummy data; seluruh teks Indonesia; tidak ada fitur di luar dokumen; pemilik proyek menyetujui.

### Tahap Backend (dimulai setelah Frontend disetujui)

| Fase | Isi | F |
| --- | --- | --- |
| B0 | Migrasi 7 tabel persis sesuai `DB_WAR.dbml`, enum, model dan relasi, seeder (`adminrt`) | — |
| B1 | Penyesuaian Breeze, login username, registrasi (BR-09), profil, reset password, timeout sesi, middleware role | F001, F002, F016 |
| B2 | Pengumuman dan upload thumbnail, landing page data nyata | F003, F004 |
| B3 | Data keluarga/warga, pengajuan perubahan data dan tambah anggota, verifikasi, transfer akun (BR-02, BR-03), tambah anggota oleh Admin, badge | F005–F008, F017 |
| B4 | Pengajuan surat, snapshot, preview, nomor surat (BR-05), PDF, batal | F011, F013–F015 |
| B5 | Iuran, lazy generation 12 bulan, nominal awal dari konfigurasi (Rp 75.000) lalu disalin dari baris terbaru, pelunasan (termasuk bulan mendatang), ubah nominal, riwayat | F009, F010, F012 |
| B6 | Otorisasi data per KK, validasi, penyimpanan berkas privat, HTTPS, pengujian | Semua |

---

## 14. Rencana Pengujian (black box, mengacu pada F)

| F | Kasus uji inti |
| --- | --- |
| F001 | Registrasi NIK baru tanpa input password; login pertama dengan password = NIK; NIK hidup ditolak; NIK pindah_kk dengan No. KK baru; No. KK sudah punya akun |
| F002 | Login warga dan admin; password salah; timeout sesi; logout |
| F003 | Hanya publish tampil; kondisi kosong |
| F004 | Tambah, ubah, hapus, ubah status, form tidak lengkap |
| F005 | Daftar anggota hanya hidup; warga tidak melihat KK lain |
| F006 | Ajukan ubah data tanpa bukti ditolak; setujui; tolak + alasan; batal; pindah_kk menyembunyikan warga |
| F007 | Tambah anggota disetujui menambah ke KK dan `user_id` akun; NIK yang sudah ada di tabel `warga` ditolak |
| F008 | Koreksi data; ubah status ekonomi; tambah anggota NIK baru; NIK di KK lain ditolak; tambah pindah_kk ke KK lain; reset password = NIK; transfer akun (BR-02 langkah 1 sampai 4, dipicu pengajuan maupun koreksi Admin) dan akun terhapus (BR-03) |
| F009 | Pelunasan; ubah nominal per baris; label status ekonomi; pop-up 2.2.17 hanya mengubah baris belum_lunas dari bulan berjalan, baris lunas tetap; pelunasan bulan mendatang menjadi lunas; KK baru dan tahun baru menyalin nominal dari baris terbaru |
| F010 | Riwayat memuat baris lunas dan belum lunas |
| F011 | Pengajuan muncul di daftar Admin |
| F012 | 12 card iuran milik KK sendiri read-only; bulan lampau belum lunas tampil MENUNGGAK; KK terdaftar tengah tahun: bulan sebelumnya N/A, tanpa baris tunggakan; buka menu berulang tidak membuat baris ganda |
| F013 | Setujui menghasilkan nomor; tolak tanpa alasan diperbolehkan |
| F014 | Status dan alasan tampil |
| F015 | PDF memuat nomor dan data snapshot; hanya surat disetujui dapat diunduh |
| F016 | Ubah no. telepon; ganti password dengan lama salah dan konfirmasi beda |
| F017 | Badge muncul saat status berubah |

Aturan bisnis tambahan: BR-05 (nomor 001 pada tahun baru, tanpa duplikat), BR-06 (tanpa baris ganda saat menu dibuka berulang), BR-07 (batal surat disetujui ditolak sistem).

---

## 15. Hal yang Belum Ditentukan

| ID | Hal | Dampak |
| --- | --- | --- |
| O-04 | Kapan badge F017 hilang (dianggap sudah dibaca) | F017 |
| O-05 | Aturan password baru ("ketentuan" di UC-03), durasi timeout sesi, batas ukuran dan tipe berkas bukti dan thumbnail | Validasi dan BR-12 |
| O-06 | Isi Beranda Admin dan Warga selain ringkasan iuran, isi "informasi layanan publik" di landing page | Layar 2.2.1, 2.2.3 |
| O-07 | Tata letak dan isi PDF surat; nama dan penyimpanan data pejabat RT untuk tanda tangan | F015 |
| O-08 | Apakah Admin dapat mengoreksi NIK dan No. KK saat koreksi data (batasan hanya untuk Warga). PNG "Pop up Ganti No KK" sudah dihapus dari desain, sehingga PRD tidak memuat fitur ubah No. KK | 2.2.10 |
| O-09 | Laporan RPL-102 perlu disamakan dengan BR-04: UC-01 masih menyebut warga mengisi kata sandi saat mendaftar (UC-02 sudah menyebut default NIK). Jumlah tabel di laporan (7) sudah sesuai DBML | Dokumentasi |
| O-11 | Judul UC-04 di laporan sama dengan UC-06 ("Mengelola Pengumuman") padahal isinya melihat pengumuman; dipertahankan | Dokumentasi |
| O-15 | Nominal baris baru disalin dari baris terbaru (BR-06). Jika Admin pernah mengubah nominal satu baris secara khusus (mis. keringanan satu KK) dan baris itu kebetulan yang terbaru, nominal tersebut ikut tersalin ke KK atau tahun baru. Alternatif: pakai nominal yang paling banyak muncul (modus) pada baris `belum_lunas` terbaru | BR-06 |
| O-16 | Belum ditentukan: warna card N/A; isi tab Riwayat Pembayaran setelah tab Kelola Status Iuran menampilkan 12 bulan; tunggakan tahun sebelumnya tidak tampil pada 12 bulan tahun berjalan (usulan: tab Riwayat menampilkan semua tahun dengan filter tahun) | 2.2.16, 2.2.26 |
| O-18 | Fungsi PNG "Detail Kelola Warga (warga)" belum jelas (detail anggota keluarga di sisi Warga?) dan tidak ada PNG untuk X6 Detail Verifikasi (kemungkinan menyatu di tab 2 Kelola Data Warga & Verifikasi) | Lampiran C.3, C.4 |

---

## Lampiran A — Label Tampilan Enum (usulan)

| Enum | Nilai dan label |
| --- | --- |
| jenis_kelamin | laki_laki: Laki-laki; perempuan: Perempuan |
| agama | islam: Islam; katolik: Katolik; protestan: Protestan; buddha: Buddha; hindu: Hindu; konghucu: Konghucu |
| pendidikan_terakhir | tidak_belum_sekolah: Tidak/Belum Sekolah; belum_tamat_sd: Belum Tamat SD; sd_sederajat: SD/Sederajat; smp_sederajat: SMP/Sederajat; sma_sederajat: SMA/Sederajat; d1_d2: D1/D2; d3: D3; d4_s1: D4/S1; s2: S2; s3: S3 |
| status_hubungan | kepala_keluarga: Kepala Keluarga; suami: Suami; istri: Istri; anak: Anak; menantu: Menantu; cucu: Cucu; orang_tua: Orang Tua; mertua: Mertua; famili_lain: Famili Lain; lainnya: Lainnya |
| status_warga | hidup: Hidup; meninggal: Meninggal; pindah_rumah: Pindah Rumah; pindah_kk: Pindah KK |
| status_tinggal | tetap: Tetap; kontrak: Kontrak; kos: Kos; menumpang: Menumpang |
| status_ekonomi | mampu: Mampu; kurang_mampu: Kurang Mampu |
| status pengajuan | menunggu: Menunggu; disetujui: Disetujui; ditolak: Ditolak; dibatalkan: Dibatalkan |
| status iuran | lunas: Lunas; belum_lunas: Belum Lunas |
| status publikasi | draft: Draft; publish: Dipublikasikan |

## Lampiran B — Bulan Romawi untuk Nomor Surat

1 I, 2 II, 3 III, 4 IV, 5 V, 6 VI, 7 VII, 8 VIII, 9 IX, 10 X, 11 XI, 12 XII. Contoh: `003/SP/RT04/RW08/X/2026`.

---

## Lampiran C — Desain UI (PNG) dan Aturan CSS

### C.1 Struktur folder

```
ui-wargaku/
├── LandingPage/   (5 file: halaman publik, login, register)
├── Warga/         (14 file)
└── AdminRT/       (23 file)
```

Total 42 file PNG. Nama file ketiga folder dicatat dari tangkapan layar terbaru (2026-10-02); satu nama di `Warga/` masih terpotong pada tangkapan layar (ditandai). Ejaan, huruf besar/kecil, dan spasi bisa berbeda tipis dari file aslinya, sehingga agent wajib mencocokkan dengan isi folder yang sebenarnya (aturan 10). Keterangan "dugaan" berarti dipetakan dari nama file saja, isi gambar belum diperiksa.

### C.2 Pemetaan file ke layar

**`ui-wargaku/LandingPage/`**

| File | ID layar | Catatan |
| --- | --- | --- |
| `Landing Page(Landing-Page).png` | 2.2.1 | Landing page |
| `Landing_Page_Pengumuman(Landing-page).png` | 2.2.1 | Dugaan: tampilan daftar pengumuman; thumbnail menyerupai halaman tersendiri, bukan hanya bagian landing page |
| `Detail Pengumuman (Landing Page).png` | X2 | Detail pengumuman |
| `Halaman Login.png` | 2.2.2 | Login |
| `Halaman Register.png` | X1 | Registrasi |

**`ui-wargaku/Warga/`**

| File | ID layar | Catatan |
| --- | --- | --- |
| `Beranda WargaKu (Warga).png` | 2.2.19 | Beranda Warga; sidebar (2.2.20) tampil di halaman ini |
| `Data Keluarga tab 1 (Warga).png` | 2.2.21 | Tab 1: anggota keluarga |
| `Riwayat Pengajuan Perubahan Data tab 2 (Warga).png` | 2.2.21 | Tab 2: riwayat pengajuan. Nama terpotong di tangkapan layar |
| `Form Daftar Anggota Keluarga Baru (Warga).png` | 2.2.22 | Dugaan: form tambah anggota baru |
| `pop up Tambah Anggota Keluarga Baru (Warga).png` | 2.2.22 | Dugaan: pop-up konfirmasi kirim pengajuan tambah anggota |
| `Form Pengajuan Perubahan Data (Warga).png` | 2.2.24 | Form ubah data |
| `Pop up Pengajuan Perubahan Data (Warga).png` | 2.2.24 | Dugaan: pop-up konfirmasi kirim pengajuan |
| `Halaman Pengajuan Surat tab 1 (Warga).png` | 2.2.25 | Tab 2: form ajukan surat |
| `Tab 2 form Pengajuan Surat (Warga).png` | 2.2.25 | Tab 1: riwayat surat |
| `Halaman Preview Surat (Warga).png` | X7 | Preview surat disetujui |
| `Halaman Iuran Warga (Warga).png` | 2.2.26 | Grid 12 card iuran (BR-06) |
| `Profil & Keamanan Akun Warga ( Warga).png` | X3 | Profil dan keamanan. Pada tangkapan layar ada spasi di dalam kurung; cocokkan dengan nama file asli |
| `Pop up Yakin Ganti password Akun Warga (Warga).png` | X3 | Pop-up konfirmasi ganti password |
| `Detail Kelola Warga (warga).png` | belum dipetakan | Lihat C.4 dan O-18 |

**`ui-wargaku/AdminRT/`**

| File | ID layar | Catatan |
| --- | --- | --- |
| `Dashboard Admin RT (Adminrt).png` | 2.2.3 | Beranda Admin; sidebar (2.2.4) tampil di halaman ini |
| `Kelola Pengumuman (Adminrt).png` | 2.2.5 | Tabel pengumuman |
| `Form Tambah Pengumuman Baru(adminrt).png` | 2.2.6 | Form tambah dan edit |
| `Pop up hapus Pengumuman(adminrt).png` | 2.2.5 | Konfirmasi hapus |
| `Kelola Data Warga & Verifikasi tab 1(Adminrt).png` | 2.2.7 | Tab Daftar Keluarga |
| `Kelola Data Warga & Verifikasi tab 2 (Adminrt).png` | 2.2.7 | Tab Verifikasi Data Warga |
| `Detail Kartu Keluarga (adminrt).png` | 2.2.8 | Detail KK |
| `Pop up Reset Password(adminrt).png` | 2.2.9 | Konfirmasi reset ke default |
| `Edit Data Warga(adminrt).png` | 2.2.10 | Form koreksi |
| `pop up confirm Edit Data Warga(adminrt).png` | 2.2.10 | Pop-up konfirmasi simpan koreksi |
| `Pop up Detail Data Warga (adminrt).png` | 2.2.11 | Pop-up detail biodata |
| `Tambah Anggota (Adminrt).png` | 2.2.12 | Form tambah anggota (BR-14) |
| `pop up confirm Tambah Anggota (adminrt).png` | 2.2.12 | Pop-up konfirmasi tambah anggota |
| `Proses Pengajuan Surat (Adminrt).png` | 2.2.13 | Daftar pengajuan surat |
| `Preview Surat Pengantar (adminrt).png` | X5 | Halaman preview surat |
| `Pop up setuju surat (adminrt).png` | 2.2.14 | Persetujuan dan penerbitan |
| `Pop up penolakan surat (adminrt).png` | 2.2.15 | Penolakan dan alasan; komponen yang sama dipakai untuk tolak verifikasi data (UC-11) |
| `Kelola Iuran warga (Adminrt).png` | 2.2.16 | Tab Kelola Status Iuran |
| `Riwayat Iuran (Adminrt).png` | 2.2.16 | Tab Riwayat Pembayaran |
| `Pop up ganti jumlah iuran (Adminrt).png` | 2.2.17 | Standar iuran bulanan |
| `Lunas Pop up (Adminrt).png` | 2.2.18 | Konfirmasi pelunasan |
| `Profil & Keamanan Akun Admin RT (Adminrt).png` | X4 | Profil dan keamanan Admin |
| `pop up yakin ganti password (Adminrt).png` | X4 | Pop-up konfirmasi ganti password |

### C.3 Layar di PRD yang tidak punya file PNG

- **2.2.4 Sidebar Admin dan 2.2.20 Sidebar Warga:** tidak ada file terpisah; diasumsikan tampil pada setiap halaman. Ikuti sidebar pada PNG beranda masing-masing.
- **X6 Detail Verifikasi:** tidak ada PNG; kemungkinan menyatu pada tab 2 Kelola Data Warga & Verifikasi (O-18). Bangun sebagai "menunggu desain" hanya jika tab 2 tidak mencukupi untuk membandingkan `data_lama` dan `data_baru`.
- **State kosong, error muat, dan error validasi (Bagian 10):** tidak ada PNG. Bangun dengan komponen yang sama dan tandai "menunggu desain".

### C.4 PNG yang tidak punya padanan di PRD

| File | Masalah | Tindakan |
| --- | --- | --- |
| `Detail Kelola Warga (warga).png` | Tidak ada layar detail anggota di sisi Warga pada katalog Bagian 9 | Jangan dibangun sampai O-18 diputuskan |

### C.5 Aturan CSS vanilla

1. Styling semua view yang ditulis di proyek ini memakai CSS biasa. Tidak ada kelas utilitas Tailwind dan tidak ada framework CSS lain. Setup Tailwind bawaan Breeze dibiarkan terpasang (tidak dihapus, tidak dipakai).
2. Susunan file di `public/css/`: `tokens.css` (custom properties), `base.css` (reset dan tipografi), `components.css` (sidebar, tabel, tab, badge, modal, form, tombol, card iuran), serta `public.css`, `warga.css`, dan `admin.css` untuk tata letak per area. Muat dengan `asset('css/...')` di layout Blade baru. Jangan menaruhnya di `resources/css/` atau memasukkannya ke Vite, supaya tidak melewati PostCSS dan Tailwind bawaan Breeze.
3. Warna, font, radius, dan jarak diambil dari PNG dan didefinisikan sekali sebagai CSS custom properties di `tokens.css`. Tidak ada nilai warna yang diulang di luar file itu.
4. Badge status memakai kelas modifier sesuai nilai enum (contoh `.badge--menunggu`, `.badge--lunas`), dan label mengikuti Lampiran A. Card iuran memakai modifier sesuai tabel BR-06 (`.iuran-card--lunas`, `--berjalan`, `--menunggak`, `--mendatang`, `--na`).
5. Penamaan kelas konsisten di seluruh proyek (disarankan BEM).
6. JavaScript: boleh dipakai seperlunya (Alpine.js bawaan Breeze atau JS vanilla di `resources/js/`). Logika bisnis tetap di server. Jika memakai library pop-up (mis. SweetAlert2), warna, bentuk, teks tombol, dan tata letaknya harus mengikuti PNG pop-up; gunakan `customClass` dan timpa dengan CSS di `components.css`, bukan CSS bawaan library. Bila tidak dapat disamakan dengan PNG, buat modal sendiri.
7. Layout baru (publik, warga, admin) tidak memuat `resources/css/app.css` dan tidak memakai `guest.blade.php` maupun `app.blade.php` bawaan Breeze, karena reset bawaan Tailwind (preflight) akan menimpa CSS vanilla. Alpine.js tetap dimuat dengan `@vite('resources/js/app.js')` saja, tanpa entri CSS. View login dan registrasi yang ditulis ulang memakai layout publik baru. Layout dan view bawaan Breeze tidak diubah.
