# 🧭 Panduan AI: Peta Konteks & Instruksi Kerja Sistem WargaKu

Dokumen ini adalah pintu masuk (*entry point*) wajib untuk setiap AI Assistant / Agent (pada akun atau sesi baru) yang bekerja di repositori ini.

---

## 1. Aturan Wajib untuk AI
1. **Dilarang Menebak / Membuat Asumsi Sendiri:**
   Sebelum mengubah kode, baca dokumen konteks di folder ini (`ai_context/`) dan file [`AGENTS.md`](file:///d:/PROJECT_PBL/WargaKu/AGENTS.md) di root.
2. **Hormati Arsitektur Service Layer:**
   Proyek ini menggunakan pola **Skinny Controller & Service Layer**:
   * Logika data kependudukan, transfer akun, dan mutasi ada di `app/Services/KependudukanService.php`.
   * Logika workflow pengajuan perubahan data, upload bukti privat, dan verifikasi ada di `app/Services/PengajuanPerubahanService.php`.
   * Logika tagihan iuran 12 bulan (*Lazy Generation*) ada di `app/Services/IuranService.php`.
   * **Jangan menaruh query database atau logika bisnis berat kembali ke dalam Controller!**
3. **Patuhi Frontend Stack:**
   * Blade Templates + **CSS Vanilla Murni (Design Tokens)** (`tokens.css`, `base.css`, `components.css`).
   * **DILARANG memakai kelas utilitas Tailwind atau Bootstrap pada view baru.**

---

## 2. Struktur File Konteks di Folder Ini (`ai_context/`)

| File | Deskripsi Isi |
| :--- | :--- |
| **`01_KRONOLOGI_PERCAKAPAN.md`** | Riwayat lengkap percakapan dari awal (Step 0) sampai Step 13757+ (7 Fase Proyek). |
| **`02_ARSITEKTUR_SERVICE_LAYER.md`** | Penjelasan rinci arsitektur Service Layer, peran tiap method, dan refaktorisasi controller. |
| **`03_ATURAN_BISNIS_BR01_BR15.md`** | Penjelasan teknis 15 Business Rules (BR-01 s/d BR-15) yang tidak boleh dilanggar. |
| **`04_STATUS_TESTING_DAN_BUGFIX.md`** | Riwayat bug yang berhasil diperbaiki dan status automated test (103 passed). |
| **`05_WACANA_DAN_ROADMAP_KELOMPOK.md`** | Catatan wacana kelompok (rencana penyederhanaan fitur, mutasi pindah KK, dll). |

---

## 3. Cara Cepat Mengetahui Kondisi Proyek
* **Framework:** Laravel 12 + MySQL InnoDB (7 Tabel Bersih: `users`, `keluarga`, `warga`, `riwayat_perubahan`, `surat`, `iuran`, `pengumuman`).
* **Kredensial Default Uji Coba:**
  * Admin RT: Username `adminrt` | Password `adminrt`
  * Warga: Username `NIK Pemilik Akun` | Password `NIK Pemilik Akun`
* **Status Pengujian Terkini:** 103 tes otomatis (464 assertions) **100% Lulus**.
