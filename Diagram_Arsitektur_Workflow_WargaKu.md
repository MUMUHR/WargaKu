# Diagram Arsitektur & Workflow — Aplikasi Informasi Warga "WargaKu"

Project PBL-TRPL102 | RT 04 | Laravel 12 + MySQL | Format: Mermaid
Disinkronkan dengan: `DB_WAR.dbml` (7 tabel, relasi keluarga via `keluarga_id`), laporan RPL-102 (revisi 1), dan `PRD_WargaKu.md` v2.6 (2026-10-02). Jika ada pertentangan, urutan prioritas mengikuti PRD Bagian 0.1; diagram ini hanya ilustrasi alur.

Daftar isi: (1) Arsitektur, (2) Aktor & fitur, (3) ERD 7 tabel, (4) State pengajuan, (5) State status warga, (6) Registrasi, (7) Perubahan data & verifikasi, (8) Transfer kepemilikan akun, (9) Autentikasi, (10) Surat, (11) Iuran, (12) Catatan sinkronisasi.

---

## 1. Arsitektur Sistem

```mermaid
graph TB
    subgraph Client["Client (Browser)"]
        W["Warga - sesi akun KK"]
        A["Admin RT - adminrt"]
    end

    subgraph App["Application Layer - Laravel 12"]
        MW["Middleware<br/>auth, role"]
        M1["Auth & Registrasi<br/>login, logout, ganti dan reset password"]
        M2["Pengumuman<br/>CRUD, thumbnail, draft atau publish"]
        M3["Data Keluarga & Warga<br/>koreksi admin, status_warga, tambah pindah_kk ke KK lain"]
        M4["Pengajuan & Verifikasi Perubahan Data<br/>ubah_data, tambah_anggota, bukti wajib, transfer akun"]
        M5["Pengajuan Surat<br/>biodata via relasi warga, nomor surat, preview, PDF on-demand"]
        M6["Iuran<br/>lazy generation 12 bulan, pelunasan, nominal"]
        BDG["Badge status pengajuan"]
    end

    subgraph Data["Data Layer"]
        DB[("MySQL - 7 tabel<br/>users, keluarga, warga, riwayat_perubahan,<br/>surat, iuran, pengumuman")]
        CFG[("File konfigurasi<br/>nominal awal iuran Rp 75.000")]
        FP[("Storage publik<br/>thumbnail pengumuman")]
        FV[("Storage privat<br/>bukti_path")]
    end

    W -->|"tanpa login"| M2
    W -->|"tanpa login"| M1

    W --> MW
    A --> MW
    MW --> M1

    MW -->|Warga| M3
    MW -->|Warga| M4
    MW -->|Warga| M5
    MW -->|"Warga, read-only"| M6
    MW -->|Warga| BDG

    MW -->|Admin| M2
    MW -->|Admin| M3
    MW -->|Admin| M4
    MW -->|Admin| M5
    MW -->|Admin| M6

    M1 --> DB
    M2 --> DB
    M3 --> DB
    M4 --> DB
    M5 --> DB
    M6 --> DB
    BDG --> DB
    M2 --> FP
    M4 --> FV
    M6 -.->|"hanya baris iuran pertama"| CFG
```

---

## 2. Aktor & Fitur

```mermaid
graph LR
    Publik(["Warga (belum login)"])
    Warga(["Warga - akun bersama per KK"])
    Admin(["Admin RT"])

    Publik --> U1["Lihat pengumuman publish - F003"]
    Publik --> U2["Registrasi akun KK - F001"]

    Warga --> W1["Login, logout, profil: no_telp dan password"]
    Warga --> W2["Lihat data keluarga dan anggota"]
    Warga --> W3["Ajukan ubah data, tambah anggota, pindah KK + bukti"]
    Warga --> W4["Batalkan pengajuan selama menunggu"]
    Warga --> W5["Ajukan surat pengantar, lihat status, unduh PDF"]
    Warga --> W6["Lihat status iuran KK - read-only"]
    Warga --> W7["Badge status pengajuan - F017"]

    Admin --> A1["Kelola pengumuman"]
    Admin --> A2["Kelola data warga/KK, status ekonomi, status_warga"]
    Admin --> A3["Verifikasi perubahan data"]
    Admin --> A4["Tambah anggota ke KK: NIK baru atau warga pindah_kk"]
    Admin --> A5["Reset password akun KK"]
    Admin --> A6["Preview dan proses surat"]
    Admin --> A7["Kelola iuran, nominal, riwayat pembayaran"]
```

---

## 3. ERD — 7 Tabel

> Semua tabel punya `created_at` dan `updated_at` (tidak digambar). `keluarga.id` adalah PK; `keluarga.no_kk` UNIQUE (identitas nomor KK, bukan kunci relasi). Semua relasi ke keluarga memakai `keluarga_id`. Constraint tambahan: `iuran` unique `(keluarga_id, bulan, tahun)`; index `warga(keluarga_id)`, `warga(status_warga)`, `keluarga(no_kk)`.

```mermaid
erDiagram
    USERS |o--o{ WARGA : "akun KK bersama"
    KELUARGA ||--o{ WARGA : beranggotakan
    KELUARGA ||--o{ IURAN : ditagih
    KELUARGA ||--o{ RIWAYAT_PERUBAHAN : "asal pengajuan"
    WARGA |o--o{ RIWAYAT_PERUBAHAN : "objek, null jika tambah_anggota"
    WARGA ||--o{ SURAT : pemohon
    WARGA ||--o{ SURAT : "tujuan: anggota KK"
    USERS |o--o{ RIWAYAT_PERUBAHAN : memverifikasi
    USERS |o--o{ PENGUMUMAN : menerbitkan

    USERS {
        int id PK
        varchar username UK "warga: NIK pemilik akun, admin: adminrt"
        varchar password
        varchar no_telp "nullable"
        enum role "admin, warga - default warga"
    }

    KELUARGA {
        int id PK "auto-increment, kunci relasi"
        varchar no_kk UK "16 digit, NOT NULL, bukan PK"
        enum status_ekonomi "mampu, kurang_mampu - NOT NULL, tanpa default di DB, aplikasi mengisi mampu"
        enum status_tinggal "tetap, kontrak, kos, menumpang - default tetap"
    }

    WARGA {
        int id PK
        int user_id FK "nullable, nullOnDelete, akun KK bersama"
        int keluarga_id FK "NOT NULL, cascadeOnDelete"
        varchar nik UK "16 digit"
        varchar nama
        varchar tempat_lahir "nullable"
        date tanggal_lahir "nullable"
        enum jenis_kelamin "laki_laki, perempuan"
        enum agama "nullable, islam, katolik, protestan, buddha, hindu, konghucu"
        enum pendidikan_terakhir "nullable, 10 nilai, tidak_belum_sekolah s.d. s3"
        varchar pekerjaan "nullable"
        text alamat "nullable"
        enum status_hubungan "10 nilai, kepala_keluarga s.d. lainnya"
        enum status_warga "hidup, meninggal, pindah_rumah, pindah_kk - default hidup"
    }

    RIWAYAT_PERUBAHAN {
        int id PK
        int warga_id FK "nullable jika tambah_anggota, nullOnDelete"
        int keluarga_id FK "NOT NULL, cascadeOnDelete"
        enum jenis_pengajuan "ubah_data, tambah_anggota"
        json data_lama "null jika tambah_anggota"
        json data_baru "NOT NULL, pindah KK: status_warga = pindah_kk"
        varchar bukti_path "NOT NULL, disk privat"
        enum status "menunggu, disetujui, ditolak, dibatalkan - default menunggu"
        text alasan "nullable"
        int admin_id FK "nullable sampai diproses, nullOnDelete"
    }

    SURAT {
        int id PK
        int pemohon_id FK "NOT NULL, pemilik akun yang login (otomatis)"
        int warga_tujuan_id FK "NOT NULL, anggota KK tujuan, dipilih via dropdown"
        text keperluan
        varchar nomor_surat UK "nullable, diisi saat disetujui, NNN/SP/RT04/bulan_romawi/tahun"
        enum status "menunggu, disetujui, ditolak, dibatalkan - default menunggu"
        text alasan "nullable"
    }

    IURAN {
        int id PK
        int keluarga_id FK "NOT NULL, cascadeOnDelete"
        decimal nominal "disalin dari baris iuran terbaru, bisa diubah per baris"
        text catatan "nullable"
        tinyint bulan "1-12"
        smallint tahun
        enum status "lunas, belum_lunas - default belum_lunas"
        datetime tanggal_bayar "nullable"
    }

    PENGUMUMAN {
        int id PK
        varchar judul
        text isi
        varchar gambar "nullable, thumbnail"
        enum status_publikasi "draft, publish - default draft"
        int admin_id FK "nullable, nullOnDelete"
    }
```

Ringkasan foreign key:

| Tabel | Kolom FK | Mengacu ke | Nullable | On delete |
| --- | --- | --- | --- | --- |
| `warga` | `user_id` | `users.id` | Ya | nullOnDelete |
| `warga` | `keluarga_id` | `keluarga.id` | Tidak | cascadeOnDelete |
| `riwayat_perubahan` | `warga_id` | `warga.id` | Ya (null jika `tambah_anggota`) | nullOnDelete |
| `riwayat_perubahan` | `keluarga_id` | `keluarga.id` | Tidak | cascadeOnDelete |
| `riwayat_perubahan` | `admin_id` | `users.id` | Ya (null sampai diproses) | nullOnDelete |
| `surat` | `pemohon_id` | `warga.id` | Tidak | Tidak ditentukan di DBML |
| `surat` | `warga_tujuan_id` | `warga.id` | Tidak | Tidak ditentukan di DBML |
| `iuran` | `keluarga_id` | `keluarga.id` | Tidak | cascadeOnDelete |
| `pengumuman` | `admin_id` | `users.id` | Ya | nullOnDelete |

---

## 4. State Machine — Status Pengajuan

Berlaku untuk `riwayat_perubahan` dan `surat`.

```mermaid
stateDiagram-v2
    [*] --> menunggu : Warga kirim pengajuan
    menunggu --> disetujui : Admin setujui
    menunggu --> ditolak : Admin tolak dan isi alasan
    menunggu --> dibatalkan : Warga batalkan
    disetujui --> [*]
    ditolak --> [*]
    dibatalkan --> [*]
    note right of ditolak
        Warga melihat alasan lalu
        membuat pengajuan baru
    end note
    note right of disetujui
        Surat disetujui tidak boleh
        dihapus atau dibatalkan
    end note
```

---

## 5. State Machine — Status Warga

```mermaid
stateDiagram-v2
    [*] --> hidup : Registrasi KK baru atau tambah anggota disetujui
    hidup --> meninggal : Pengajuan disetujui atau koreksi Admin
    hidup --> pindah_rumah : Pengajuan disetujui atau koreksi Admin
    hidup --> pindah_kk : Pengajuan pindah KK disetujui atau koreksi Admin
    pindah_kk --> hidup : Admin tambahkan ke KK lain atau daftar KK baru
    meninggal --> [*]
    pindah_rumah --> [*]
    note right of hidup
        Setiap keluar dari status hidup
        memicu pengecekan kepemilikan akun
        lihat diagram 8
    end note
```

---

## 6. Workflow Registrasi Akun KK (F001)

```mermaid
flowchart TD
    Start(["Buka halaman registrasi"]) --> Isi["Isi NIK, No.KK, data identitas, no_telp<br/>tanpa input password"]
    Isi --> CekNIK{"NIK sudah ada<br/>di tabel warga?"}

    CekNIK -- "Belum ada" --> CekKK{"Keluarga dengan No.KK ini<br/>sudah punya akun?<br/>lookup keluarga.no_kk"}
    CekKK -- "Sudah" --> Arah(["Tolak: arahkan ke Login<br/>atau ajukan tambah anggota via akun KK"])
    CekKK -- "Belum" --> Keluarga["Buat keluarga jika belum ada<br/>simpan no_kk, status_ekonomi = mampu<br/>ambil keluarga.id"]
    Keluarga --> Insert["Insert warga: keluarga_id = keluarga.id<br/>status hidup"]

    CekNIK -- "Ada, status hidup" --> TolakAktif(["Tolak: NIK sudah aktif,<br/>arahkan ke Login"])
    CekNIK -- "Ada, meninggal atau pindah_rumah" --> Hubungi(["Tolak: hubungi Admin RT"])
    CekNIK -- "Ada, status pindah_kk" --> CekKKBaru{"Keluarga dengan No.KK tujuan<br/>sudah ada?"}
    CekKKBaru -- "Sudah ada" --> MintaAdmin(["Arahkan: minta Admin RT<br/>menambahkan ke KK tersebut - F008"])
    CekKKBaru -- "Belum ada" --> KeluargaBaru["Buat keluarga baru dari No.KK tujuan<br/>update warga: keluarga_id = keluarga baru, status hidup"]

    Insert --> BuatAkun["Buat users: username = NIK, role warga<br/>password awal = NIK, disimpan ter-hash"]
    KeluargaBaru --> BuatAkun
    BuatAkun --> SetUser["Set warga.user_id = users.id"]
    SetUser --> Dash(["Dashboard Warga"])
```

---

## 7. Workflow Perubahan Data & Verifikasi (F006, F007, F008)

```mermaid
flowchart TD
    subgraph WargaSide["Warga"]
        S(["Halaman Data Keluarga"]) --> Aksi{"Pilih aksi"}
        Aksi -- "Ubah data" --> F1["Ubah field yang diperlukan"]
        Aksi -- "Tambah anggota" --> F2["Isi data anggota baru"]
        Aksi -- "Pindah atau pecah KK" --> F3["Pilih warga yang pindah<br/>data_baru = status_warga pindah_kk"]
        F1 --> Bukti["Upload bukti - wajib, disk privat"]
        F2 --> Bukti
        F3 --> Bukti
        Bukti --> Kirim["Simpan riwayat_perubahan: keluarga_id = KK warga<br/>data_lama = snapshot, status menunggu"]
        Kirim --> Batal{"Warga batalkan<br/>sebelum diproses?"}
        Batal -- "Ya" --> Dibatal(["Status dibatalkan"])
    end

    subgraph AdminSide["Admin RT - Tab Verifikasi Data Warga"]
        Kirim --> Rev{"Review data_lama vs data_baru<br/>dan bukti"}
        Rev -- "Tolak" --> Tolak(["Status ditolak + alasan<br/>data warga tidak berubah"])
        Rev -- "Setujui" --> Trx["Mulai transaksi DB"]
        Trx --> Jenis{"jenis_pengajuan"}
        Jenis -- "tambah_anggota" --> Ins["Insert warga baru<br/>keluarga_id = riwayat_perubahan.keluarga_id<br/>user_id = akun KK, status hidup"]
        Jenis -- "ubah_data" --> Upd["Update field warga<br/>sesuai data_baru"]
        Upd --> Keluar{"data_baru.status_warga<br/>bukan hidup?"}
        Keluar -- "Ya" --> Transfer[["Transfer kepemilikan akun - diagram 8"]]
        Keluar -- "Tidak" --> Done
        Ins --> Done
        Transfer --> Done["Status disetujui, admin_id tercatat<br/>badge ke Warga"]
    end
```

---

## 8. Workflow Transfer Kepemilikan Akun

Business rule backend pada Verifikasi Perubahan Data (F008 / UC-11). Dipicu setiap kali `status_warga` seorang warga berubah dari `hidup` ke status lain (pindah_kk, meninggal, pindah_rumah), baik lewat pengajuan disetujui maupun koreksi Admin. Pemilik akun = warga dengan `nik` sama dengan `users.username`. Seluruh anggota KK berbagi `warga.user_id` yang sama.

```mermaid
flowchart TD
    Start(["Warga W keluar dari status hidup"]) --> SetStatus["Baca akun_lama = W.user_id<br/>Set W.status_warga = status baru, W.user_id = NULL"]
    SetStatus --> CekOwner{"W.nik = akun_lama.username?<br/>W pemilik akun"}

    CekOwner -- "Tidak" --> Selesai(["Selesai: akun KK tetap aktif"])
    CekOwner -- "Ya" --> Sisa{"Masih ada warga dengan keluarga_id sama<br/>status_warga = hidup?"}

    Sisa -- "Tidak ada" --> Hapus["Dalam satu transaksi: hapus akun_lama<br/>warga.user_id yang masih menunjuk otomatis NULL<br/>FK nullOnDelete"]
    Hapus --> Selesai2(["Selesai: KK tidak aktif, tidak ditagih iuran"])

    Sisa -- "Ada" --> Pilih["Pilih pemilik baru berurutan:<br/>1. Kepala keluarga<br/>2. Istri atau suami<br/>3. Anak tertua usia 17+<br/>4. Anggota tertua"]
    Pilih --> Ganti["akun_lama.username = NIK pemilik baru<br/>password tidak diubah"]
    Ganti --> Selesai3(["Selesai: keluarga tersisa tetap bisa login<br/>dengan password yang sama"])
```

---

## 9. Workflow Autentikasi & Reset Password

```mermaid
flowchart TD
    subgraph Login["Login"]
        L0(["Buka halaman login"]) --> L1["Input username dan password<br/>warga: NIK, admin: adminrt"]
        L1 --> L2{"Kredensial valid?"}
        L2 -- "Tidak" --> L3["Tampilkan pesan error"]
        L3 --> L1
        L2 -- "Ya" --> L4(["Dashboard sesuai role<br/>tanpa paksaan ganti password"])
        L4 --> TO{"Tidak aktif melewati batas waktu?<br/>durasi: O-05"}
        TO -- "Ya" --> L0
    end

    subgraph Reset["Reset password oleh Admin RT"]
        R0(["Admin pilih akun KK, konfirmasi reset"]) --> R1["password = NIK pemilik akun saat ini<br/>users.username"]
    end

    subgraph Seed["Seeder"]
        S0["Akun adminrt dibuat<br/>password awal adminrt"]
    end

    R1 -.-> L0
    S0 -.-> L0
```

Tidak ada lupa-password lewat email; warga yang lupa meminta Admin RT melakukan reset.

---

## 10. Workflow Pengajuan Surat Pengantar (F011–F013)

```mermaid
flowchart TD
    Start(["Warga buka Ajukan Surat"]) --> Pemohon["Pemohon = pemilik akun yang login<br/>pemohon_id otomatis, tanpa input"]
    Pemohon --> Tujuan["Pilih anggota KK untuk surat<br/>dropdown anggota hidup dengan keluarga_id yang sama<br/>warga_tujuan_id terisi, biodata auto-fill via relasi warga, terkunci"]
    Tujuan --> Kep["Isi keperluan"]
    Kep --> Simpan["Simpan surat: pemohon_id, warga_tujuan_id, keperluan<br/>status menunggu, tanpa salinan biodata"]

    Simpan --> Batal{"Warga batalkan?"}
    Batal -- "Ya, status masih menunggu" --> Dibatal(["Status dibatalkan"])
    Batal -- "Status sudah disetujui" --> Blok(["Ditolak sistem:<br/>surat disetujui wajib diarsip"])

    Simpan --> Preview["Admin buka Halaman Preview Surat"]
    Preview --> Putus{"Keputusan Admin"}
    Putus -- "Tolak" --> Tolak(["Status ditolak + alasan opsional"])
    Putus -- "Setujui" --> NoSurat["Generate nomor_surat unik<br/>NNN/SP/RT04/bulan_romawi/tahun<br/>status disetujui"]
    NoSurat --> Unduh(["Warga preview dan unduh PDF ber-TTD RT<br/>dirender saat diunduh, biodata diambil via relasi warga_tujuan_id"])
```

---

## 11. Workflow Iuran & Pengaturan Standar (F009, F012)

```mermaid
flowchart TD
    subgraph Gen["Lazy generation tagihan 12 bulan - BR-06"]
        B1(["Warga atau Admin buka menu Iuran"]) --> B2["Tentukan bulan dan tahun berjalan, zona Asia/Jakarta<br/>Bulan awal tagih per KK:<br/>Januari jika tahun keluarga.created_at lebih lama,<br/>selain itu bulan keluarga.created_at"]
        B2 --> B3{"Setiap KK aktif punya baris<br/>dari bulan awal tagih sampai Desember?"}
        B3 -- "Sudah" --> B6
        B3 -- "Belum" --> B4["KK aktif = minimal 1 anggota hidup<br/>Nominal disalin dari baris iuran terbaru:<br/>1. belum_lunas terbaru<br/>2. jika tidak ada, baris terbaru apa pun<br/>3. jika tabel kosong, Rp 75.000 dari config"]
        B4 --> B5["Insert baris yang kurang: keluarga_id, bulan, tahun, belum_lunas<br/>unique keluarga_id, bulan, tahun menjaga duplikasi"]
        B5 --> B6["Tampilkan tagihan"]
    end

    subgraph Set["Pop-up Standar Iuran Bulanan - 2.2.17"]
        A1(["Admin buka pengaturan"]) --> A2["Input nominal baru"]
        A2 --> A3["UPDATE nominal pada baris belum_lunas<br/>dari bulan berjalan sampai Desember<br/>baris lunas dan tunggakan tidak berubah"]
    end

    A3 -.->|"baris berikutnya menyalin nominal baru"| B4

    subgraph AdminIuran["Admin RT"]
        B6 --> C1["Tab Kelola Status Iuran:<br/>cari dan filter KK, status ekonomi, 12 bulan per KK"]
        C1 --> C2["Pelunasan: status lunas + tanggal_bayar<br/>bulan mana pun yang punya baris, termasuk bulan mendatang dan tunggakan<br/>baris tetap ada"]
        C1 --> C3["Ubah nominal atau catatan per baris"]
        B6 --> C4["Tab Riwayat Pembayaran"]
    end

    B6 --> W1["Warga: 12 card read-only"]
    W1 --> W2["Tampilan turunan, bukan enum:<br/>Lunas, Berjalan, Menunggak,<br/>Belum masuk periode, N/A Dikecualikan"]
```

---

## 12. Catatan Sinkronisasi

Diagram ini sudah disamakan dengan `DB_WAR.dbml` terbaru (7 tabel; skema final, tidak diubah lagi) dan aturan bisnis PRD v2.6. Perubahan skema yang direfleksikan di seluruh diagram:

| Hal | Yang berlaku |
| --- | --- |
| Kunci relasi keluarga | `keluarga.id` (INT auto-increment) adalah PK. `warga`, `riwayat_perubahan`, dan `iuran` memakai `keluarga_id` (FK ke `keluarga.id`, NOT NULL, cascadeOnDelete), bukan lagi `no_kk` |
| `keluarga.no_kk` | VARCHAR(16), UNIQUE, NOT NULL; hanya identitas nomor KK. Dipakai sebagai input registrasi dan pencarian, lalu di-resolve ke `keluarga.id` |
| Unique `iuran` | `(keluarga_id, bulan, tahun)` |
| Index | `warga(keluarga_id)`, `warga(status_warga)`, `keluarga(no_kk)` |
| Penamaan tabel | `pengajuan_perubahan_data` menjadi `riwayat_perubahan`; `pengajuan_surat` menjadi `surat`. Nama modul dan fitur di UI tidak berubah |
| Tabel `surat` | Kolom `admin_id` dihapus (verifikasi surat bersifat kolektif oleh Admin RT). Kolom snapshot (`nama_lengkap`, `tempat_tanggal_lahir`, `jenis_kelamin`, `agama`, `alamat`) dihapus; biodata diambil lewat relasi Eloquent `warga_tujuan_id` |
| Konsekuensi tanpa snapshot | PDF surat yang sudah disetujui dirender dari data `warga` saat diunduh, sehingga ikut berubah bila data warga diperbarui setelah surat disetujui |
| FK nullable dan on delete | `warga.user_id`, `riwayat_perubahan.warga_id`, `riwayat_perubahan.admin_id`, dan `pengumuman.admin_id` nullable dengan nullOnDelete. `warga.keluarga_id`, `riwayat_perubahan.keluarga_id`, dan `iuran.keluarga_id` NOT NULL dengan cascadeOnDelete. `surat.pemohon_id` dan `surat.warga_tujuan_id` NOT NULL (on delete tidak ditentukan di DBML) |
| `warga.user_id` | Berlaku untuk seluruh anggota KK; pemilik akun = `warga.nik = users.username`. FK nullOnDelete: saat akun dihapus (BR-03), `warga.user_id` otomatis NULL; aplikasi tetap menghapus akun dalam satu transaksi |
| Tabel `settings` | Tidak ada. Nominal awal iuran Rp 75.000 di file konfigurasi, selanjutnya disalin dari baris `iuran` terbaru (BR-06) |
| `keluarga.status_ekonomi` | NOT NULL tanpa default di DB; aplikasi mengisi `mampu` (BR-11) |
| Password registrasi | Tidak ada input; password awal = NIK, reset Admin = NIK pemilik akun saat ini (BR-04) |
| Tagihan iuran | 12 bulan tahun berjalan mulai bulan awal tagih KK; bulan sebelumnya N/A (tanpa baris) |
| Transfer akun | Berlaku saat `hidup` berubah ke `pindah_kk`, `meninggal`, atau `pindah_rumah`, lewat pengajuan maupun koreksi Admin (BR-02) |
| Pindah KK | Dicatat sebagai `ubah_data` dengan `data_baru.status_warga = pindah_kk` |
| Tambah anggota oleh Admin | Aksi langsung, tidak melalui `riwayat_perubahan`; NIK `pindah_kk` ditarik ke KK tujuan dengan mengganti `keluarga_id` (BR-14) |
| Surat pengantar | Hanya untuk anggota KK sendiri (tanpa mode manual); `pemohon_id` otomatis dari akun login, `warga_tujuan_id` dipilih dari dropdown dan selalu diisi |
| Nomor surat | `NNN` sekuensial, reset tiap tahun kalender (BR-05) |

**Masih terbuka (PRD Bagian 15):** O-04 (badge hilang kapan), O-05 (aturan password, timeout, ukuran berkas), O-15 (nominal tersalin dari baris yang diubah khusus), O-16 (warna N/A dan isi tab Riwayat).
