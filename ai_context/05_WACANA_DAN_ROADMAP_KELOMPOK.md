# 💡 05. Wacana & Roadmap Diskusi Kelompok

Dokumen ini mencatat hal-hal yang sedang didiskusikan oleh tim kelompok PBL untuk pengambilan keputusan fitur ke depan.

---

## 1. Wacana Penyederhanaan Modul Perubahan Data Warga
* **Latar Belakang:**  
  Modul pengajuan perubahan data saat ini adalah modul terpanjang dan paling rumit karena memiliki *approval workflow 2-fase*, mesin komparasi snapshot 10 atribut JSON lama vs baru, dan modal review *side-by-side*.
* **Gagasan Penyederhanaan yang Diusulkan:**
  1. Warga **hanya dapat mengajukan Pendaftaran Anggota Baru** (misal: kelahiran bayi) beserta bukti dokumen.
  2. Warga **tidak dapat mengedit biodata sendiri**. Jika ada salah ketik nama, pergantian pekerjaan, atau perbaikan data lainnya, warga melapor langsung ke Admin RT (misal via WhatsApp atau tatap muka).
  3. Admin RT melakukan koreksi data secara langsung melalui tombol *Edit/Koreksi Data Warga* di panel admin (fitur ini sudah ada dan sudah selesai di `KependudukanService::koreksiDataWarga`).
* **Dampak Teknis:**
  * Menghilangkan seluruh logika komparasi *diff engine* `data_lama` vs `data_baru`.
  * Menghilangkan kerumitan modal UI perbandingan warna-warni.
  * Logika verifikasi Admin menjadi 1 alur linier: `Warga::create(...)` saat disetujui.
* **Status:**  
  *Wacana / Bahan diskusi internal kelompok PBL sebelum diputuskan final.*

---

## 2. Kesepakatan Terkait "Transfer Akun Otomatis" & "Pindah KK"
* **Hasil Analisis Teknis:**
  * Kedua fitur ini terbukti **sangat ringan dan sederhana di kodingan backend (~30 baris kode)**.
  * Tidak membebani antarmuka (UI) sama sekali karena berjalan di background saat admin mengubah status warga.
* **Rekomendasi:**
  * **Wajib dipertahankan** karena merupakan nilai jual utama (*Grade A+*) saat presentasi di depan dosen penguji, membuktikan bahwa tim memikirkan penanganan siklus hidup data (*data lifecycle*) nyata di masyarakat.

---

## 3. Catatan Pembahasan Final: Penyederhanaan Modul Warga & Keluarga
* **Tanggal diskusi:** 2026-10-29
* **Kesimpulan utama:**
  * Modul warga/keluarga perlu dipangkas dari sisi fitur agar lebih sesuai skala sistem administrasi RT yang sederhana, bukan sistem data kependudukan yang terlalu berat.
  * Kompleksitas terbesar berasal dari alur **self-service edit biodata warga** dan **approval workflow perubahan data** yang mencampurkan master data, bukti dokumen, dan logika review UI.

### 3.1. Scope fitur yang perlu dipangkas
* **Dihapus dari akses warga:**
  1. Mengubah biodata warga sendiri.
  2. Mengubah alamat, pekerjaan, pendidikan, status hubungan, dan atribut master lainnya tanpa persetujuan admin.
  3. Form edit anggota keluarga di area warga.
  4. Review komparasi *side-by-side* untuk seluruh atribut data.
* **Dibiarkan tetapi disederhanakan:**
  1. Pendaftaran anggota baru oleh warga.
  2. Pengajuan surat.
  3. Lihat riwayat keluarga, status iuran, dan pengumuman.
* **Dipindahkan sepenuhnya ke admin RT:**
  1. Mutasi warga.
  2. Pindah KK.
  3. Ubah status warga (`hidup`, `meninggal`, `pindah_rumah`, `pindah_kk`).
  4. Transfer akun otomatis atas dasar BR-02/BR-03.
  5. Koreksi master data yang bersifat legal atau administratif.

### 3.2. Prinsip penyederhanaan yang disepakati
* **Warga bukan pengelola master data.**
  * Warga cukup mengajukan kebutuhan atau permintaan.
  * Admin RT yang menjalankan perubahan legal administrasi.
* **Mutasi/pindah KK harus admin-only.**
  * Ini lebih aman, lebih konsisten dengan BR-12 dan BR-14, serta mengurangi resiko duplikasi atau perubahan data yang tidak terkendali.
* **Komparasi harus dipangkas menjadi data yang diajukan, bukan snapshot lengkap.**
  * Jika fitur edit dihapus, maka tidak perlu lagi mesin diff `data_lama` vs `data_baru` yang rumit untuk seluruh atribut.
  * Komparasi cukup berisi: data yang diajukan, alasan, bukti pendukung, dan keputusan admin.
* **Riwayat perubahan tetap dipertahankan, tetapi lebih fokus dan lebih ringkas.**
  * Contoh penggunaan yang tepat: tambah anggota baru, mutasi, pindah KK, perubahan status, penolakan/penyetujuan admin.

### 3.3. Mengenai database
* **Tidak disarankan untuk mengurangi tabel sekarang.**
  * Struktur database sudah disepakati dalam PRD/DBML, yaitu 7 tabel utama.
  * Yang harus disederhanakan adalah kompleksitas fitur aplikasi, bukan struktur basis data resmi.
* **Yang bisa diubah adalah payload/usage data, bukan skema utama.**
  * `riwayat_perubahan` tetap ada dan tetap relevan, tetapi boleh dipakai untuk catatan ringkas dan bersifat administrasi.
  * Data yang disimpan bisa dibuat lebih minimal jika memang tidak digunakan lagi.

### 3.4. Pola model bisnis yang diusulkan
* **Area Warga:**
  1. Lihat data keluarga
  2. Daftar anggota keluarga
  3. Ajukan surat
  4. Ajukan tambah anggota baru
  5. Lihat iuran dan pengumuman
  6. Melapor ke admin untuk perubahan data yang memerlukan koreksi legal
* **Area Admin RT:**
  1. Koreksi master data warga
  2. Menerima dan memverifikasi pengajuan tambah anggota
  3. Memproses mutasi/pindah KK
  4. Menjalankan transfer akun otomatis sesuai BR-02/BR-03
  5. Menyetujui atau menolak surat dan perubahan administratif

### 3.5. Keputusan final yang disepakati
* **Edit biodata warga oleh warga dihapus.**
* **Komparasi lengkap data lama vs data baru tidak diperlukan lagi** setelah fitur edit dihapus.
* **Mutasi dan pindah KK sepenuhnya dikelola admin.**
* **Tabel database tetap dipertahankan sesuai PRD/DBML.**
* **Penyederhanaan difokuskan pada fitur dan alur, bukan struktur data inti.**

### 3.6. Dampak teknis yang diharapkan
* Mengurangi kompleksitas controller dan service.
* Mengurangi logika review dan diff engine.
* Meningkatkan kejelasan respons admin dan audit kependudukan.
* Menjaga konsistensi aturan bisnis seperti BR-01 sampai BR-15.
* Lebih cocok untuk target proyek RT 04 yang menuntut sistem administrasi yang rapi, aman, dan tidak terlalu berat di sisi UI/backend.

---

## 4. Status Kesepakatan
* **Status:** Disetujui sebagai arah penyederhanaan yang lebih realistis untuk implementasi berikutnya.
* **Prioritas berikutnya:**
  1. Hilangkan flow edit biodata dari area warga.
  2. Maksimalkan peran admin untuk mutasi dan perubahan legal.
  3. Sederhanakan review pengajuan menjadi data yang diajukan + alasan + bukti pendukung.
  4. Jaga skema database tetap sesuai PRD/DBML, fokus pada fitur bukan relasi tabel.

---

## 5. Rencana Penyederhanaan Database Ramah Mahasiswa Semester 1 (Relational On-Demand Model)
* **Tanggal Wacana:** 2026-10-31
* **Latar Belakang Mahasiswa Semester 1:**
  Konsep kolom JSON (`data_lama` & `data_baru`), serialization snapshot, dan *diff engine* terlalu kompleks untuk materi basis data dasar semester 1. Tim kelompok membutuhkan skema database yang normal, mudah diinspeksi langsung lewat phpMyAdmin, dan logikanya linier (cukup memanggil relasi Eloquent).

### 5.1. Konsep Utama: Pola Relasi `warga_id` (Identik dengan Tabel `surat`)
Sama seperti tabel `surat` yang tidak menduplikasi nama/biodata melainkan hanya menyimpan `warga_tujuan_id`, tabel pengajuan anggota baru disederhanakan murni berbasis relasi:
* Di tabel `warga`, ditambahkan status keberadaan baru: `menunggu_verifikasi` (atau memanfaatkan kolom status yang ada).
* Tabel pengajuan (misal `riwayat_perubahan` / `pengajuan_anggota`) dipangkas drastis menjadi sangat ringan:
  ```
  id | warga_id | keluarga_id | bukti_path | status | alasan | admin_id | timestamps
  ```
* Kolom JSON `data_lama` dan `data_baru` **dihapus total**.

### 5.2. Alur Bisnis Baru (Super Simpel & Linier)
1. **Warga Mengajukan Anggota Baru:**
   * Data diinput via form dan disimpan langsung ke tabel `warga` dengan `status_warga = 'menunggu_verifikasi'` (sehingga tidak muncul di daftar warga aktif yang berstatus `hidup`).
   * Record pengajuan dibuat dengan menunjuk `warga_id` tersebut beserta lampiran `bukti_path` dan `status = 'menunggu'`.
2. **Menampilkan Data di UI (Cukup Manggil Relasi):**
   * Di view/Blade maupun preview admin, tidak perlu decode JSON:
     * `$pengajuan->warga->nama`
     * `$pengajuan->warga->nik`
     * `$pengajuan->warga->tanggal_lahir`
     * `$pengajuan->warga->status_hubungan`
3. **Admin Memverifikasi:**
   * **Jika Disetujui:**
     * Status warga diaktifkan: `$pengajuan->warga->update(['status_warga' => 'hidup'])`.
     * Status pengajuan diselesaikan: `$pengajuan->update(['status' => 'disetujui'])`.
   * **Jika Ditolak:**
     * Record calon warga dihapus atau ditandai ditolak: `$pengajuan->warga->delete()`.
     * Pengajuan dicatat ditolak: `$pengajuan->update(['status' => 'ditolak', 'alasan' => $alasan])`.

### 5.3. Keuntungan untuk Presentasi & Sidang Semester 1
1. **Normalisasi Database Murni (3NF):** Dosen penguji basis data akan sangat menyukai tidak adanya redundansi data maupun data tak terstruktur (JSON).
2. **Kodingan Sangat Pendek:** Menghilangkan ~150 baris kode mesin komparasi atribut.
3. **Mudah Dipresentasikan Teman Kelompok:** Teman sekelompok dapat menjelaskan alur relasi Eloquent dasar `belongsTo(Warga::class)` dengan sangat mudah dan percaya diri.


