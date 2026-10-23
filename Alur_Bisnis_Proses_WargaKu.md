# Alur Bisnis Proses — Aplikasi Informasi Warga "WargaKu"

**Project PBL-TRPL102 | Lingkup Wilayah RT 04**  
*Format Dokumentasi: Activity Diagram Swimlane (Mermaid) & Penjelasan Alur Sistem*

Dokumen ini memetakan proses bisnis kependudukan dan administrasi rukun tetangga, membandingkan antara proses konvensional manual (**Current / As-Is**) dengan sistem digital terintegrasi aplikasi WargaKu (**To-Be**), sesuai spesifikasi PRD v2.8 dan Dokumen SRS (Lampiran 1).

---

## DAFTAR ISI
1. [Activity Diagram Utama: Current (As-Is / Manual)](#1-activity-diagram-utama-current-as-is--manual)
2. [Activity Diagram Utama: To-Be (Sistem WargaKu)](#2-activity-diagram-utama-to-be-sistem-wargaku)
3. [Rincian Alur Bisnis per Modul Sistem](#3-rincian-alur-bisnis-per-modul-sistem)
   - 3.1 [Registrasi & Akses Akun KK](#31-alur-registrasi--akses-akun-kk)
   - 3.2 [Pengajuan Surat Pengantar Daring](#32-alur-pengajuan-surat-pengantar-daring)
   - 3.3 [Perubahan Data Keluarga & Mutasi Kependudukan](#33-alur-perubahan-data-keluarga--mutasi-kependudukan)
   - 3.4 [Pengelolaan Iuran Warga (12 Bulan)](#34-alur-pengelolaan-iuran-warga-12-bulan)
   - 3.5 [Publikasi Pengumuman Digital](#35-alur-publikasi-pengumuman-digital)
4. [Tabel Komparasi Proses Bisnis (Current vs To-Be)](#4-tabel-komparasi-proses-bisnis-current-vs-to-be)

---

## 1. Activity Diagram Utama: Current (As-Is / Manual)

Diagram ini menggambarkan alur kerja pengurusan administrasi warga konvensional sebelum adanya aplikasi. Proses bertumpu pada kehadiran fisik, pemeriksaan dokumen manual di tempat, pengetikan mandiri oleh pengurus RT, dan pengarsipan berbasis kertas map fisik.

```mermaid

flowchart TD
    subgraph LW["WARGA"]
        W_START(( )) --> W1["Datang langsung ke rumah atau pos RT"]
        W1 --> W2["Menyampaikan keperluan secara lisan"]
        W2 --> W3["Menyerahkan dokumen pendukung<br/>(KK, KTP, dll)"]
        W_WAIT["Menunggu hasil proses"]
        W_DEC{"Hasil sesuai?"}
        W_FIX["Melengkapi dokumen atau memperbaiki data"]
        W_RCV["Menerima surat / informasi dari RT"]
        W_TAKE["Mengambil surat fisik"]
        W_USE["Menggunakan surat di instansi tujuan"]
        W_END((( )))

        W_DEC -- "Tidak" --> W_FIX
        W_FIX --> W_WAIT
        W_DEC -- "Ya" --> W_RCV
        W_RCV --> W_TAKE
        W_TAKE --> W_USE
        W_USE --> W_END
    end

    subgraph LA["ADMIN RT"]
        A1["Menerima dan memeriksa dokumen secara visual"]
        A_DEC{"Dokumen valid?"}
        A_REJ["Menolak / meminta dokumen diulang"]
        A2["Mencatat pada buku agenda<br/>(termasuk nomor urut)"]
        A3["Mencocokkan data dengan arsip KK manual"]
        A4["Mengetik surat pengantar<br/>(nomor surat manual dari buku agenda)"]
        A5["Tanda tangan dan cap basah RT"]
        A6["Mengarsipkan fotokopi surat di map / arsip fisik"]

        A1 --> A_DEC
        A_DEC -- "Tidak" --> A_REJ
        A_REJ --> A2
        A_DEC -- "Ya" --> A2
        A2 --> A3
        A3 --> A4
        A4 --> A5
        A5 --> A6
    end

    subgraph LS["SISTEM (MANUAL)"]
        S1["Menyimpan data pada buku agenda,<br/>arsip fisik, dan dokumen cetak"]
    end

    W3 --> A1
    A5 --> W_WAIT
    W_WAIT --> W_DEC
    A6 --> S1
```

### Kelemahan Proses Manual (Current):
1. **Inefisiensi Waktu**: Warga harus datang langsung ke rumah pengurus RT pada jam luang pengurus dan sering kali harus menunggu tanpa kepastian.
2. **Keterbatasan Pelacakan**: Tidak ada pencatatan riwayat perubahan data kependudukan lama (data langsung ditimpa atau dicoret di buku fisik).
3. **Risiko Redundansi & Kesalahan Manusia**: Penomoran surat manual dari buku agenda rawan ganda atau terlewat saat pengurus berganti.
4. **Kerentanan Arsip**: Arsip kertas berisiko rusak, terselip, atau hilang dimakan usia dan bencana fisik.

---

## 2. Activity Diagram Utama: To-Be (Sistem WargaKu — Versi Proyek Selesai)

Diagram ini menggambarkan alur kerja digital terintegrasi yang **sudah selesai diimplementasikan secara riil pada aplikasi WargaKu**, memisahkan tanggung jawab antara antarmuka **Warga**, otomasi **Sistem Backend**, dan kendali verifikasi **Admin RT**.

```mermaid
flowchart TD
    subgraph LW["WARGA"]
        W_START(( )) --> W_LOG["Login ke aplikasi WargaKu<br/>(username = NIK, password = NIK)"]
        W_LOG --> W_DASH["Masuk ke Dashboard Warga"]
        W_DASH --> W_MENU["Pilih menu layanan:<br/>Ajukan Surat / Data Keluarga / Iuran / Pengumuman"]
        W_MENU --> W_FORM["Isi formulir pengajuan:<br/>• Surat: pilih anggota KK, isi keperluan<br/>• Data Keluarga: ubah data/status atau tambah anggota, upload bukti"]
        W_FORM --> W_SEND["Kirim pengajuan (status: menunggu)"]
        
        W_CANCEL{"Batalkan pengajuan?"}
        W_CANCELED(["Status dibatalkan<br/>(otomatis keluar dari antrean Admin)"])
        
        W_NOTIF["Menerima pembaruan status pengajuan<br/>(badge sidebar & menu riwayat)"]
        W_DEC{"Status pengajuan?"}
        W_REJ["Melihat alasan penolakan di riwayat<br/>(dapat memperbaiki & mengajukan ulang)"]
        W_ACC["Melihat data keluarga terupdate ATAU<br/>Preview & Cetak/Unduh Surat PDF (format A4)"]
        W_END((( )))

        W_CANCEL -- "Ya (selama masih menunggu)" --> W_CANCELED
        W_CANCEL -- "Tidak" --> S_ALERT
        W_DEC -- "Ditolak" --> W_REJ
        W_DEC -- "Disetujui" --> W_ACC
        W_ACC --> W_END
        W_REJ --> W_FORM
    end

    subgraph LS["SISTEM (WARGAKU)"]
        S_VAL{"Validasi data input<br/>(NIK, No. KK, format berkas)<br/>[BR-01, BR-04]"}
        S_ERR["Kirim pesan validasi kesalahan ke form warga"]
        S_SAVE["Simpan pengajuan berstatus menunggu<br/>dan rekam riwayat perubahan data snapshot"]
        S_ALERT["Munculkan di antrean verifikasi Admin RT<br/>(badge pengajuan baru)"]
        
        S_UP["Update status pengajuan:<br/>Disetujui / Ditolak / Dibatalkan"]
        S_SURAT["Generate nomor surat unik otomatis<br/>(NNN/SP/RT04/bulan_romawi/tahun) [BR-05]"]
        S_MUTASI["Eksekusi mutasi data keluarga &<br/>transfer kepemilikan akun otomatis [BR-02/BR-03]"]
        S_PDF["Render tampilan cetak surat standar A4<br/>(@media print dengan TTD & cap RT)"]
    end

    subgraph LA["ADMIN RT"]
        A_LOG["Login ke aplikasi WargaKu (Admin RT)"] --> A_DASH["Buka menu Verifikasi Pengajuan / Surat"]
        A_DASH --> A_VIEW["Periksa rincian pengajuan:<br/>• Surat: modal preview draf surat<br/>• Data Keluarga: komparasi data lama vs baru & tab berkas bukti"]
        A_VIEW --> A_CHECK["Verifikasi keabsahan data & dokumen"]
        A_CHECK --> A_DEC{"Keputusan Admin?"}
        
        A_REJ_ACT["Tolak pengajuan dengan mengisi alasan penolakan"]
        A_ACC_ACT["Setujui pengajuan warga"]
        
        A_DEC -- "Tolak" --> A_REJ_ACT
        A_DEC -- "Setujui" --> A_ACC_ACT
    end

    W_SEND --> S_VAL
    S_VAL -- "Tidak valid" --> S_ERR
    S_ERR --> W_FORM
    S_VAL -- "Valid" --> S_SAVE
    S_SAVE --> W_CANCEL

    A_REJ_ACT --> S_UP
    A_ACC_ACT --> S_UP

    S_UP --> W_NOTIF
    W_NOTIF --> W_DEC

    S_UP -- "Jika Surat Disetujui" --> S_SURAT
    S_SURAT --> S_PDF
    S_PDF --> W_ACC

    S_UP -- "Jika Data Keluarga Disetujui" --> S_MUTASI
    S_MUTASI --> W_ACC
```

---

## 3. Rincian Alur Bisnis per Modul Sistem

### 3.1 Alur Registrasi & Akses Akun KK

1. **Warga** membuka halaman registrasi tanpa memasukkan kata sandi. Warga menginput NIK, No. KK, data identitas lengkap, nomor WhatsApp/telepon aktif, dan **alamat domisili lokal RT 04 (Blok & Nomor Rumah)**.
2. **Sistem** memvalidasi NIK & No. KK sesuai aturan bisnis **BR-09**:
   - Jika NIK sudah terdaftar aktif (`hidup`), registrasi ditolak dan diarahkan ke halaman login.
   - Jika NIK berstatus `pindah_kk`, sistem mengizinkan pendaftaran KK baru dan memperbarui data kependudukannya.
   - Jika lolos, sistem otomatis membuat akun dengan `username = NIK` dan `password = NIK` (terenkripsi *Bcrypt*).
3. **Admin RT** bertindak sebagai pemulih akun: jika warga lupa kata sandi, Admin RT memiliki tombol aksi untuk mereset kata sandi akun KK kembali ke NIK pemilik akun secara aman.

```mermaid
flowchart TD
    subgraph LW["Warga"]
        W1(["Buka halaman registrasi"]) --> W2["Isi NIK, No. KK, biodata, no. telepon<br/>alamat domisili (Blok & No. Rumah di RT 04)<br/>tanpa input password"]
        W3(["Masuk Dashboard Warga"])
        W4["Login: username = NIK, password = NIK<br/>(dapat ubah password di menu Profil)"]
        W5["Lupa password: minta reset ke Admin RT"]
    end

    subgraph LS["Sistem"]
        S1{"Validasi NIK dan No. KK<br/>(BR-09)"}
        S2(["Tolak registrasi:<br/>NIK aktif diarahkan login<br/>No. KK ganda diarahkan login / tambah anggota"])
        S3["Buat keluarga baru bila belum ada<br/>Buat akun user (username=NIK, password=NIK)<br/>Kaitkan relasi warga.user_id"]
    end

    subgraph LA["Admin RT"]
        A1["Reset kata sandi akun KK<br/>kembali ke NIK pemilik akun"]
    end

    W2 --> S1
    S1 -- "Tidak Lolos" --> S2
    S1 -- "Lolos" --> S3
    S3 --> W3
    W4 --> W3
    W5 --> A1
    A1 --> W4
```

---

### 3.2 Alur Pengajuan Surat Pengantar Daring

1. **Warga** memilih menu *Ajukan Surat*. Formulir menyediakan dropdown anggota keluarga yang berstatus hidup; biodata pemohon (nama, tempat/tgl lahir, pekerjaan, agama, alamat) terisi otomatis (*autofill*) dan terkunci guna mencegah manipulasi. Warga hanya mengisi kolom keperluan surat.
2. Selama pengajuan berstatus `menunggu`, warga dapat membatalkannya melalui tombol *Batalkan*.
3. **Admin RT** membuka menu *Proses Pengajuan Surat*, meninjau berkas permohonan melalui *modal preview* draf surat:
   - Jika ditolak: Admin memberikan alasan penolakan.
   - Jika disetujui: Sistem otomatis men-generate **nomor surat resmi unik** dengan format `NNN/SP/RT04/{bulan_romawi}/{tahun}` (**BR-05**).
4. **Warga** dapat langsung melihat status disetujui dan mencetak/mengunduh surat berformat standar kertas A4 (`@media print`) lengkap dengan tanda tangan digital dan stempel RT.

```mermaid
flowchart TD
    subgraph LW["Warga"]
        W1(["Buka Ajukan Surat"]) --> W2["Pilih anggota KK<br/>(biodata autofill & terkunci)"]
        W2 --> W3["Isi keperluan surat lalu kirim"]
        W4{"Batalkan?"}
        W5(["Status dibatalkan<br/>(keluar dari antrean Admin)"])
        W6["Lihat catatan alasan penolakan<br/>(dapat ajukan ulang)"]
        W7(["Preview & Cetak / Unduh PDF<br/>(format standar A4 ber-TTD/cap RT)"])
    end

    subgraph LS["Sistem"]
        S1["Simpan pengajuan surat<br/>status: menunggu verifikasi"]
        S2["Generate nomor surat unik otomatis<br/>(NNN/SP/RT04/bulan_romawi/tahun)"]
        S3["Simpan alasan penolakan, status: ditolak"]
        S4["Siapkan format cetak surat A4 (@media print)"]
    end

    subgraph LA["Admin RT"]
        A1["Tinjau permohonan di Modal Preview Surat"]
        A2{"Keputusan Admin"}
    end

    W3 --> S1
    S1 --> W4
    W4 -- "Ya (selagi menunggu)" --> W5
    W4 -- "Tidak" --> A1
    A1 --> A2
    A2 -- "Setujui" --> S2
    A2 -- "Tolak + alasan" --> S3
    S2 --> S4
    S4 --> W7
    S3 --> W6
```

---

### 3.3 Alur Perubahan Data Keluarga & Mutasi Kependudukan

1. **Warga** membuka menu *Data Keluarga* dan memilih salah satu dari dua aksi:
   - **Ubah Data / Status Warga**: Mengoreksi biodata atau memperbarui status (*hidup, meninggal, pindah rumah, pindah KK*). NIK dan No. KK dikunci.
   - **Tambah Anggota Baru**: Mengisi data lengkap anggota baru yang baru lahir atau baru bergabung dalam KK.
2. Warga mengunggah berkas bukti pendukung (**mendukung multi-upload file JPG/PNG/PDF**) lalu mengirim pengajuan.
3. Warga dapat membatalkan pengajuan sebelum diverifikasi admin.
4. **Admin RT** memeriksa pengajuan pada tab verifikasi yang membandingkan secara berdampingan: *Data Lama vs Data Baru* beserta penampil berkas bukti (*multi-document tab*).
5. **Otomasi Sistem saat Disetujui**:
   - Jika status warga berubah keluar dari `hidup` (meninggal/pindah): Sistem secara otomatis mengeksekusi **BR-02 (Transfer Kepemilikan Akun KK)** ke anggota keluarga lain yang masih hidup (hierarki: Kepala Keluarga baru -> Suami/Istri -> Anak tertua 17+ -> Anggota tertua).
   - Jika seluruh anggota KK telah pindah/meninggal: Akun dinonaktifkan/dihapus otomatis (**BR-03**).

```mermaid
flowchart TD
    subgraph LW["Warga"]
        W1(["Buka Data Keluarga"]) --> W2{"Pilih Aksi"}
        W2 -- "Ubah data / status" --> W3["Pilih anggota, ubah data/status<br/>(meninggal, pindah rumah, pindah KK)"]
        W2 -- "Tambah anggota" --> W4["Isi data lengkap anggota baru"]
        W3 --> W5["Upload berkas bukti wajib<br/>(mendukung multi-file JPG, PNG, PDF)"]
        W4 --> W5
        W5 --> W6["Kirim pengajuan"]
        W7{"Batalkan pengajuan?"}
        W8(["Status dibatalkan<br/>(otomatis keluar antrean)"])
        W9["Lihat riwayat & alasan penolakan"]
    end

    subgraph LS["Sistem"]
        S1["Simpan snapshot data_lama & data_baru<br/>status: menunggu, bukti di disk privat"]
        S2["Mulai transaksi basis data"]
        S3{"Jenis Pengajuan"}
        S4["Tambah: insert warga baru ke KK"]
        S5["Ubah: update atribut data warga"]
        S6{"Status keluar dari hidup?"}
        S7["Eksekusi Transfer Akun Otomatis (BR-02/BR-03)"]
        S8["Status disetujui, mutasi data tersimpan"]
        S9["Status ditolak, data warga tidak berubah"]
    end

    subgraph LA["Admin RT"]
        A1["Tab Verifikasi: komparasi data lama vs baru<br/>dan preview multi-dokumen bukti"]
        A2{"Keputusan Admin"}
    end

    W6 --> S1
    S1 --> W7
    W7 -- "Ya (selagi menunggu)" --> W8
    W7 -- "Tidak" --> A1
    A1 --> A2
    A2 -- "Setujui" --> S2
    A2 -- "Tolak + alasan" --> S9
    S2 --> S3
    S3 -- "tambah_anggota" --> S4
    S3 -- "ubah_data" --> S5
    S5 --> S6
    S6 -- "Ya" --> S7
    S6 -- "Tidak" --> S8
    S4 --> S8
    S7 --> S8
    S8 --> W9
    S9 --> W9
```

---

### 3.4 Alur Pengelolaan Iuran Warga (12 Bulan)

Aplikasi WargaKu menggunakan pencatatan non-payment gateway (pembayaran riil tetap tunai ke pengurus RT) dengan sistem digital terpadu:
1. **Penerbitan Malas (*Lazy Generation*)**: Saat warga atau admin membuka modul iuran, sistem otomatis menerbitkan kartu tagihan bulanan dari bulan awal terdaftar hingga Desember tanpa duplikasi.
2. **Warga**: Memiliki tampilan transparan berupa kartu 12 bulan kalender (*Januari s.d. Desember*) yang menunjukkan status tagihan: *Lunas* (beserta tanggal bayar), *Menunggak*, atau *Belum Masuk Periode*.
3. **Admin RT**: Mengonfirmasi pelunasan secara manual saat menerima uang kas, melihat rekap penerimaan tahunan per KK, dan dapat menyesuaikan tarif iuran standar melalui pop-up pengaturan (penyesuaian tarif hanya berlaku untuk bulan berjalan ke depan).

```mermaid
flowchart TD
    subgraph LW["Warga"]
        W1["Bayar uang iuran tunai ke RT"]
        W2(["Buka menu Iuran Warga"])
        W3["Pantau kartu 12 bulan kalender<br/>(Lunas + Tanggal Bayar / Menunggak)"]
    end

    subgraph LS["Sistem"]
        S1["Lazy Generation tagihan bulanan per KK<br/>(Januari s.d. Desember tanpa duplikasi)"]
        S2["Update status lunas & catat tanggal_bayar"]
        S3["Perbarui nominal tagihan bulan mendatang<br/>(tagihan lunas & tunggakan lama tidak berubah)"]
    end

    subgraph LA["Admin RT"]
        A1["Terima pembayaran tunai warga"]
        A2["Cari KK pada Kelola Iuran lalu pilih bulan"]
        A3["Klik Konfirmasi Pelunasan"]
        A4["Atur standar tarif iuran bulanan"]
        A5["Pantau riwayat pembayaran & panel rekap"]
    end

    W1 --> A1
    A1 --> A2
    A2 --> S1
    A2 --> A3
    A3 --> S2
    S2 --> A5
    A4 --> S3
    W2 --> S1
    S1 --> W3
    S2 --> W3
```

---

### 3.5 Alur Publikasi Pengumuman Digital

1. **Admin RT** membuat pengumuman dengan judul, rincian informasi, kategori, dan thumbnail opsional.
2. Admin RT dapat menentukan status: disimpan sebagai **Draft** (hanya terlihat oleh pengurus) atau **Publish** (diterbitkan luas).
3. **Warga & Masyarakat** dapat langsung membaca pengumuman berstatus *Publish* pada halaman beranda depan (*Landing Page*) secara publik tanpa harus login terlebih dahulu.

```mermaid
flowchart TD
    subgraph LA["Admin RT"]
        A1(["Kelola Pengumuman"]) --> A2["Buat / Edit Pengumuman:<br/>Judul, Isi, Kategori, Thumbnail"]
        A2 --> A3{"Pilih Status Publikasi"}
        A4["Ubah status atau hapus pengumuman"]
    end

    subgraph LS["Sistem"]
        S1["Simpan sebagai Draft<br/>(tersembunyi dari publik)"]
        S2["Simpan sebagai Publish<br/>(ditampilkan di landing page & portal warga)"]
    end

    subgraph LW["Warga & Tamu"]
        W1(["Akses Landing Page Publik (tanpa login)"])
        W2["Melihat kartu pengumuman, carousel informasi,<br/>dan rincian isi pengumuman lengkap"]
    end

    A3 -- "Draft" --> S1
    A3 -- "Publish" --> S2
    S1 -.-> A4
    S2 -.-> A4
    A4 -.-> S2
    S2 --> W2
    W1 --> W2
```

---

## 4. Tabel Komparasi Proses Bisnis (Current vs To-Be)

| Parameter Proses | Proses Manual (Current / As-Is) | Sistem WargaKu (To-Be) | Dampak & Nilai Tambah |
| :--- | :--- | :--- | :--- |
| **Penyampaian Keperluan** | Warga harus mendatangi rumah pengurus RT secara langsung membawa berkas fisik. | Pengajuan dilakukan secara daring melalui ponsel/laptop kapan saja dari rumah. | Menghemat waktu dan tenaga warga; pengurus RT tidak terganggu jam istirahatnya. |
| **Identitas & Formulir** | Ditulis tangan atau diisi manual secara berulang di kertas; rawan salah tulis biodata. | Biodata pemohon otomatis ditarik dari basis data KK yang valid (*autofill & locked*). | Mencegah *human-error* dan manipulasi data identitas warga. |
| **Pemeriksaan Dokumen** | Dokumen fotokopi diperiksa sekilas dengan mata telanjang; arsip fisik rentan tercecer. | Berkas bukti diunggah langsung (*multi-upload* gambar/PDF) dan ditinjau pada tab verifikasi sistem. | Dokumen tersimpan rapi pada penyimpanan privat digital yang aman dan dapat dilacak kapan pun. |
| **Kepastian & Status** | Warga tidak tahu progres surat dan harus datang berulang kali untuk menanyakan. | Status terpantau secara *real-time* (*Menunggu*, *Disetujui*, *Ditolak* lengkap dengan alasan, atau *Dibatalkan*). | Transparansi penuh; warga memperoleh kepastian status tanpa perlu bertanya langsung. |
| **Penomoran Surat** | Ditulis manual dari buku agenda; rentan nomor ganda atau terlompat. | Dibuat otomatis oleh sistem dengan format standar resmi penomoran unik per tahun (**BR-05**). | Menjamin integritas urutan arsip surat dan standar formalitas kelembagaan RT. |
| **Output Dokumen** | Diketik manual satu per satu di word processor lalu dicetak dan dicap basah. | Sistem langsung merender tampilan cetak A4 terstandarisasi (`@media print`) siap unduh/cetak mandiri. | Format surat seragam, rapi, dan cepat terbit hitungan detik setelah disetujui. |
| **Pencatatan Iuran** | Dicatat di buku kas atau spreadsheet manual; warga tidak bisa memeriksa riwayatnya sendiri. | Dasbor kartu 12 bulan interaktif per KK; warga dapat melihat histori pelunasan secara transparan. | Mencegah selisih pencatatan uang kas dan mempermudah rekap keuangan bagi pengurus RT. |
| **Penyebaran Informasi** | Mengandalkan secarik kertas di pos ronda atau pesan broadcast chat yang mudah tertimbun. | Diterbitkan di *Landing Page* publik terpusat dengan thumbnail, filter, dan status publikasi. | Informasi RT tersampaikan serentak, rapi, dan memiliki arsip berita yang mudah ditelusuri. |
