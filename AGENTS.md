# Pedoman & Konteks Tetap Sistem Informasi "WargaKu" (PBL-TRPL102)

File ini dibaca secara otomatis oleh AI Assistant / Agent pada setiap sesi dan akun baru untuk menjaga kesinambungan konteks proyek.

---

## 1. Identitas & Tech Stack Proyek
* **Nama Proyek:** WargaKu — Sistem Informasi Kependudukan, Layanan Surat, & Tata Kelola Iuran RT 04.
* **Framework Backend:** Laravel 12 + MySQL (InnoDB) 7 Tabel Bersih (`users`, `keluarga`, `warga`, `riwayat_perubahan`, `surat`, `iuran`, `pengumuman`).
* **Frontend:** Blade Templates + **CSS Vanilla Murni (Design Tokens)** (`tokens.css`, `base.css`, `components.css`) + Boxicons. **Dilarang memakai kelas utilitas Tailwind/Bootstrap.**
* **Arsitektur:** Menggunakan **Service Layer (Skinny Controller)**:
  * `app/Services/KependudukanService.php` (Manajemen data KK, warga, koreksi biodata, mutasi, transfer akun BR-02/BR-03, reset password BR-04, pindah KK BR-14).
  * `app/Services/PengajuanPerubahanService.php` (Workflow approval 2-fase ubah data & tambah anggota, diff engine snapshot JSON, upload bukti privat).
  * `app/Services/IuranService.php` (Lazy generation iuran 12 bulan BR-06, pelunasan, standar tarif).

---

## 2. Aturan Bisnis Inti (Wajib Dipatuhi)
1. **BR-01:** Satu Akun KK Bersama (username = NIK pemilik akun aktif). Seluruh anggota KK berbagi `warga.user_id` yang sama.
2. **BR-02 & BR-03:** Transfer Akun Otomatis jika pemilik akun keluar dari status `hidup` (hierarki pewaris: Pasangan &rarr; Anak &ge;17 thn &rarr; Anggota Tertua). Password tidak berubah. Jika seluruh anggota KK meninggal/pindah, akun dihapus dalam transaksi DB.
3. **BR-04:** Password pendaftaran baru = NIK. Reset password hanya oleh Admin RT ke NIK pemilik akun aktif. Tidak ada reset mandiri via email.
4. **BR-05:** Format nomor surat resmi sekuensial `NNN/SP/RT04/BULAN_ROMAWI/TAHUN`, reset ke 001 tiap 1 Januari.
5. **BR-06:** Lazy Generation iuran 12 bulan tahun berjalan. KK terdaftar tengah tahun berstatus N/A untuk bulan lampau tanpa baris di database.
6. **BR-07:** Surat yang telah disetujui tidak dapat dibatalkan atau dihapus (arsip permanen).
7. **BR-08:** Berkas bukti identitas disimpan di disk privat (`storage/app/private/bukti`).
8. **BR-09:** Validasi registrasi ketat menolak NIK aktif, NIK meninggal/pindah tanpa rekomendasi admin, dan No KK ganda.
9. **BR-10:** Tidak ada snapshot biodata di tabel `surat`. Biodata dirender on-demand via relasi Eloquent `warga_tujuan_id`.
10. **BR-14:** Anggota pindah KK diubah statusnya menjadi `pindah_kk`, lalu dialihkan ke KK baru oleh Admin RT dengan mengganti `keluarga_id` tanpa membuat duplikasi baris baru.

---

## 3. Dokumen Referensi Wajib
Sebelum melakukan modifikasi kode atau fitur baru, Agent **wajib merujuk pada file-file berikut**:
* **Kronologi Lengkap & Keputusan:** `CATATAN_KONSULTASI_DAN_PROGRES_WARGAKU.md`
* **Spesifikasi & Dokumen Induk:** `PRD_WargaKu.md`
* **Diagram Arsitektur & Alur:** `Diagram_Arsitektur_Workflow_WargaKu.md` & `Alur_Bisnis_Proses_WargaKu.md`
* **Skema Database:** `DB_WAR.dbml`
* **Buku Panduan PDF Resmi:** `Panduan_Lengkap_WargaKu.pdf`
