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
