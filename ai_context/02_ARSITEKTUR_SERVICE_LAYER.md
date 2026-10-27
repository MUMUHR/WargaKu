# 🏗️ 02. Arsitektur & Pola Service Layer WargaKu

Dokumen ini menjelaskan implementasi Clean Architecture (*Skinny Controller, Fat Service*) pada WargaKu.

---

## 1. Mengapa Service Layer Dibuat?
Sebelumnya, `Admin\WargaController.php` membengkak hingga **634 baris**. Controller menangani validasi HTTP, query database, manipulasi transaksi, upload file privat, mutasi kependudukan, hingga algoritma transfer akun. Hal ini melanggar *Single Responsibility Principle (SRP)*.

Dengan memisahkan ke **Service Layer**:
* **Controller** hanya bertugas: Menerima HTTP Request &rarr; Memanggil Service &rarr; Me-return View atau Redirect.
* **Service** bertugas: Menjalankan logika bisnis, kalkulasi data, transaksi database (`DB::transaction`), upload berkas, dan penegakan aturan bisnis.

---

## 2. Struktur Service Layer

### 📁 `app/Services/KependudukanService.php`
Mengelola entitas kependudukan riil (`Keluarga` dan `Warga`):
* `hitungStatistikKependudukan(): array`  
  Menghitung total KK aktif, warga hidup, gender laki-laki/perempuan, dan jumlah antrean verifikasi untuk dashboard admin.
* `getKeluargaQuery(Request $request): Builder`  
  Membangun query pencarian Kartu Keluarga dengan filter pencarian teks (No KK, Nama Warga, NIK, Alamat), filter status tinggal, dan filter status ekonomi.
* `tambahAnggotaOlehAdmin(Keluarga $keluarga, array $data): Warga`  
  Mengimplementasikan **BR-14**:
  * Jika NIK baru &rarr; `Warga::create(...)` dengan status `hidup` dan `user_id` akun KK.
  * Jika NIK berstatus `pindah_kk` &rarr; Alihkan ke KK baru (ganti `keluarga_id`, kembalikan status ke `hidup`, dan tautkan ke `user_id` akun baru).
  * Jika NIK sudah ada dan aktif &rarr; Tolak dengan `InvalidArgumentException`.
* `koreksiDataWarga(Warga $warga, array $data): Warga`  
  Memperbarui biodata warga. Jika status warga berubah keluar dari `hidup` (meninggal/pindah rumah/pindah KK), otomatis memanggil `$warga->prosesTransferAkunOtomatis()` (**BR-02**).
* `ubahStatusKeluarga(Keluarga $keluarga, string $statusTinggal, string $statusEkonomi): Keluarga`  
  Mengubah status ekonomi dan status tinggal satu Kartu Keluarga.
* `resetPasswordKeluarga(Keluarga $keluarga): User`  
  Me-reset kata sandi akun KK menjadi NIK pemilik akun aktif saat itu (**BR-04**).
* `hapusAnggota(Warga $warga): void`  
  Proteksi: Kepala keluarga tidak boleh dihapus jika masih memiliki tanggungan hidup. Memicu transfer akun sebelum data dihapus.
* `hapusKeluarga(Keluarga $keluarga): void`  
  Menghapus Kartu Keluarga beserta akun login secara transaksional (`DB::transaction`).

---

### 📁 `app/Services/PengajuanPerubahanService.php`
Mengelola alur persetujuan (*approval system*) dan berkas bukti (`Riwayat_Perubahan`):
* `buatPengajuanUbahData(Keluarga $keluarga, Warga $warga, array $input, $buktiFiles, ?string $alasan): Riwayat_Perubahan`  
  Mendeteksi atribut yang berubah secara selektif, mengunggah berkas bukti, dan menyimpan draft `ubah_data` dengan status `menunggu`.
* `buatPengajuanTambahAnggota(Keluarga $keluarga, array $input, $buktiFiles): Riwayat_Perubahan`  
  Mengunggah berkas bukti dan menyimpan draft `tambah_anggota` dengan status `menunggu`.
* `batalkanPengajuan(Riwayat_Perubahan $pengajuan): bool`  
  Membatalkan pengajuan warga jika statusnya masih `menunggu`.
* `verifikasiPengajuan(Riwayat_Perubahan $pengajuan, string $tindakan, ?string $alasan, int $adminId): void`  
  Mengeksekusi persetujuan atau penolakan di dalam `DB::transaction()`:
  * Jika disetujui: memperbarui data warga atau membuat individu baru, lalu memicu transfer akun jika ada status yang keluar dari `hidup`.
  * Jika ditolak: status diubah menjadi `ditolak` dan mencatat alasan penolakan.
* `uploadBerkasBukti(mixed $bukti): string`  
  Menangani penyimpanan berkas bukti ke folder `public/uploads/bukti/` dengan penamaan aman `bukti_timestamp_uniqid.ext`.
* `deteksiPerubahanData(Warga $warga, array|Request $input, Keluarga $keluarga): array`  
  Mesin komparasi (*diff engine*) untuk membandingkan 10 kolom biodata dan menghasilkan snapshot `[$dataLama, $dataBaru]`.

---

### 📁 `app/Services/IuranService.php`
Mengelola tata kelola iuran warga:
* `ambilNominalStandar(): float`  
  Menentukan tarif iuran bulanan berdasarkan 3 prioritas BR-06.
* `generateTagihan12BulanKk(Keluarga $keluarga, int $tahun): void`  
  Mekanisme *Lazy Generation* 12 bulan (Januari–Desember) tahun berjalan.
* `catatPelunasan(Iuran $iuran, ?string $catatan): Iuran`  
  Mencatat pembayaran iuran dan mengisi `tanggal_bayar`.
* `aturStandarIuran(int $tahun, int $bulanMulai, float $nominalBaru): int`  
  Memperbarui baris `belum_lunas` mulai bulan berjalan s/d akhir tahun.

---

## 3. Pola Dependency Injection pada Controller

Contoh pada `Admin\WargaController.php`:
```php
class WargaController extends Controller
{
    public function __construct(
        protected KependudukanService $kependudukanService,
        protected PengajuanPerubahanService $pengajuanService
    ) {}

    public function verifikasiPengajuan(Request $request, int $id): RedirectResponse
    {
        // 1. Validasi HTTP Request
        $validated = $request->validate([
            'tindakan' => ['required', Rule::in(['setujui', 'tolak'])],
            'alasan'   => ['nullable', 'string', 'max:500'],
        ]);

        // 2. Delegasikan seluruh logika bisnis ke Service
        $this->pengajuanService->verifikasiPengajuan(
            Riwayat_Perubahan::findOrFail($id),
            $validated['tindakan'],
            $validated['alasan'] ?? null,
            auth()->id()
        );

        // 3. Return HTTP Response
        return redirect()->back()->with('success', 'Pengajuan berhasil diproses.');
    }
}
```
Ukuran controller menjadi sangat ramping (~315 baris) dan mudah dibaca!
