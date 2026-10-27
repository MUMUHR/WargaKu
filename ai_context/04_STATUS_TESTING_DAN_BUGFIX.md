# 🧪 04. Status Testing & Riwayat Perbaikan Bug

Dokumen ini mencatat seluruh bug yang pernah diselesaikan serta status pengujian otomatis (*automated tests*) terkini.

---

## 1. Riwayat Bug yang Berhasil Diperbaiki

### Bug A: Hilangnya Status Hubungan Keluarga (SHDK) Anak
* **Gejala:** Saat warga mengedit data anggota keluarga dengan hubungan anak, dropdown tidak terpilih dan hasil pengajuan tidak mencatat kolom hubungan keluarga.
* **Akar Masalah:** Terjadi ketidaksesuaian pemetaan istilah antara teks antarmuka ("Anak Kandung") dengan nilai enum murni database (`anak`).
* **Solusi:** Seluruh dropdown dan pemetaan disederhanakan murni menggunakan enum database `anak`. Form edit dan modal review kini berfungsi normal dan akurat.

### Bug B: Avatar Inisial Dua Huruf Nama Warga
* **Kasus:** Avatar inisial warga perlu menampilkan 2 huruf kapital yang diambil dari kata pertama dan kedua nama warga (misal: "Budi Santoso" &rarr; "BS").
* **Solusi:** Diterapkan logika:
  ```php
  $avatar = strtoupper(substr($words[0] ?? 'W', 0, 1) . substr($words[1] ?? ($words[0] ?? 'G'), 0, 1));
  ```
  Berfungsi mulus untuk nama 1 kata maupun banyak kata.

### Bug C: Penamaan Berkas Bukti Acak & Unik
* **Kasus:** Menghindari penumpukan nama file yang sama saat banyak warga mengunggah berkas.
* **Solusi:** Menggunakan format standar industri:
  ```php
  $namaBerkas = 'bukti_' . time() . '_' . uniqid() . '.' . $ext;
  ```
  Disimpan ke disk aman dan terhindar dari tabrakan nama file.

---

## 2. Status Automated Test Suite Terkini
Pengujian dijalankan menggunakan framework PHPUnit / Pest bawaan Laravel:

```text
   PASS  Tests\Feature\Auth\AuthenticationTest
   PASS  Tests\Feature\Auth\RegistrationTest
   PASS  Tests\Feature\IuranServiceTest
   PASS  Tests\Feature\PengumumanTest
   PASS  Tests\Feature\ProfilTest
   PASS  Tests\Feature\WargaDashboardDanKeluargaTest
   PASS  Tests\Feature\WargaIuranTest
   PASS  Tests\Feature\WargaSuratTest

   Tests:    103 passed (464 assertions)
   Duration: 23.12s
```

* **Total Pengujian:** 103 tes fitur.
* **Total Assertions:** 464 asersi.
* **Tingkat Kelulusan:** **100% PASS (Zero Failures).**
* Seluruh skenario penolakan registrasi (BR-09), mutasi data (BR-08, BR-14), transfer akun (BR-02/BR-03), penomoran surat (BR-05), dan tagihan iuran (BR-06) terbukti valid secara otomatis.
