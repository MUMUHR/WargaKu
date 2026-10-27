<?php

namespace App\Services;

use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use DomainException;

/**
 * Service Layer untuk Mengelola Data Kependudukan (Kartu Keluarga & Warga)
 * 
 * Bertanggung jawab menangani seluruh logika bisnis entitas kependudukan,
 * termasuk penambahan anggota (BR-14), koreksi biodata, mutasi status keberadaan,
 * transfer akun otomatis (BR-02/BR-03), dan reset kata sandi akun keluarga (BR-04).
 */
class KependudukanService
{
    /**
     * Mengambil ringkasan statistik kependudukan untuk dashboard dan panel admin
     */
    public function hitungStatistikKependudukan(): array
    {
        return [
            'totalKkAktif' => Keluarga::whereHas('wargas', fn ($q) => $q->where('status_warga', 'hidup'))->count(),
            'totalWargaHidup' => Warga::where('status_warga', 'hidup')->count(),
            'totalLakiLaki' => Warga::where('status_warga', 'hidup')->where('jenis_kelamin', 'laki_laki')->count(),
            'totalPerempuan' => Warga::where('status_warga', 'hidup')->where('jenis_kelamin', 'perempuan')->count(),
            'totalMenungguVerifikasi' => Riwayat_Perubahan::where('status', 'menunggu')->count(),
        ];
    }

    /**
     * Membangun query daftar Kartu Keluarga beserta filter pencarian dan status
     */
    public function getKeluargaQuery(Request $request): Builder
    {
        $query = Keluarga::with([
            'kepalaKeluarga' => fn ($q) => $q->where('status_warga', 'hidup'),
            'wargas' => fn ($q) => $q->where('status_warga', 'hidup'),
        ])->withCount([
            'wargas as jumlah_anggota_hidup' => fn ($q) => $q->where('status_warga', 'hidup'),
        ]);

        // Filter pencarian berdasarkan No KK, Nama Warga, NIK, atau Alamat
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('no_kk', 'like', "%{$search}%")
                    ->orWhereHas('wargas', function ($wq) use ($search) {
                        $wq->where('nama', 'like', "%{$search}%")
                            ->orWhere('alamat', 'like', "%{$search}%")
                            ->orWhere('nik', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Status Tinggal
        if ($request->filled('status_tinggal')) {
            $query->where('status_tinggal', $request->input('status_tinggal'));
        }

        // Filter Status Ekonomi
        if ($request->filled('status_ekonomi')) {
            $query->where('status_ekonomi', $request->input('status_ekonomi'));
        }

        return $query;
    }

    /**
     * Menambahkan anggota keluarga baru oleh Admin RT (BR-08 & BR-14)
     *
     * Menangani 3 skenario:
     * 1. NIK baru belum terdaftar -> buat record baru
     * 2. NIK ada dengan status 'pindah_kk' -> alihkan ke KK tujuan dan hidupkan kembali
     * 3. NIK sudah ada dan aktif di KK sama atau KK lain -> lempar exception
     *
     * @throws InvalidArgumentException
     */
    public function tambahAnggotaOlehAdmin(Keluarga $keluarga, array $data): Warga
    {
        $nik = trim($data['nik'] ?? '');
        if (empty($nik)) {
            throw new InvalidArgumentException('Nomor Induk Kependudukan (NIK) wajib diisi.');
        }

        $existingWarga = Warga::where('nik', $nik)->first();
        $akunKk = $this->ambilAkunKeluarga($keluarga);
        $userId = $akunKk?->id;

        $jenisKelamin = Warga::normalisasiJenisKelamin($data['jenis_kelamin'] ?? $data['jk'] ?? null);
        $statusHubungan = Warga::normalisasiStatusHubungan($data['status_hubungan'] ?? $data['shdk'] ?? null);
        $agama = Warga::normalisasiAgama($data['agama'] ?? null);
        $pendidikan = Warga::normalisasiPendidikan($data['pendidikan_terakhir'] ?? $data['pendidikan'] ?? null);
        $alamat = $data['alamat'] ?? $keluarga->alamat;

        if ($existingWarga) {
            // Skenario: NIK sudah ada pada KK yang sama
            if ($existingWarga->keluarga_id == $keluarga->id && $existingWarga->status_warga === 'hidup') {
                throw new InvalidArgumentException('NIK sudah terdaftar pada Kartu Keluarga ini.');
            }

            // Skenario: NIK terdaftar dengan status pindah_kk -> pindahkan ke KK baru (BR-14)
            if ($existingWarga->status_warga === 'pindah_kk') {
                $existingWarga->update([
                    'keluarga_id' => $keluarga->id,
                    'user_id' => $userId,
                    'nama' => $data['nama'],
                    'tempat_lahir' => $data['tempat_lahir'] ?? $existingWarga->tempat_lahir,
                    'tanggal_lahir' => $data['tanggal_lahir'] ?? $existingWarga->tanggal_lahir,
                    'jenis_kelamin' => $jenisKelamin ?? $existingWarga->jenis_kelamin,
                    'agama' => $agama ?? $existingWarga->agama,
                    'pendidikan_terakhir' => $pendidikan ?? $existingWarga->pendidikan_terakhir,
                    'pekerjaan' => $data['pekerjaan'] ?? $existingWarga->pekerjaan,
                    'alamat' => $alamat,
                    'status_hubungan' => $statusHubungan ?? $existingWarga->status_hubungan,
                    'status_warga' => 'hidup',
                ]);

                return $existingWarga;
            }

            // Skenario: NIK terdaftar di KK lain dengan status aktif/meninggal
            throw new InvalidArgumentException('NIK sudah terdaftar dalam Kartu Keluarga lain.');
        }

        // Skenario: NIK belum ada di sistem -> buat baru
        return Warga::create([
            'keluarga_id' => $keluarga->id,
            'user_id' => $userId,
            'nik' => $nik,
            'nama' => $data['nama'],
            'tempat_lahir' => $data['tempat_lahir'] ?? null,
            'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $jenisKelamin ?? 'laki_laki',
            'agama' => $agama,
            'pendidikan_terakhir' => $pendidikan,
            'pekerjaan' => $data['pekerjaan'] ?? null,
            'alamat' => $alamat,
            'status_hubungan' => $statusHubungan ?? 'anak',
            'status_warga' => 'hidup',
        ]);
    }

    /**
     * Memperbarui/mengoreksi data master warga oleh Admin RT (BR-02 & BR-03)
     *
     * @throws InvalidArgumentException
     */
    public function koreksiDataWarga(Warga $warga, array $data): Warga
    {
        $statusLama = $warga->status_warga;
        $statusBaru = Warga::normalisasiStatusWarga($data['status_warga'] ?? $data['status_keberadaan'] ?? null) ?? $statusLama;

        $updateData = [
            'nama' => $data['nama'] ?? $warga->nama,
            'tempat_lahir' => $data['tempat_lahir'] ?? $warga->tempat_lahir,
            'tanggal_lahir' => $data['tanggal_lahir'] ?? $warga->tanggal_lahir,
            'jenis_kelamin' => Warga::normalisasiJenisKelamin($data['jenis_kelamin'] ?? $data['jk'] ?? null) ?? $warga->jenis_kelamin,
            'agama' => Warga::normalisasiAgama($data['agama'] ?? null) ?? $warga->agama,
            'pendidikan_terakhir' => Warga::normalisasiPendidikan($data['pendidikan_terakhir'] ?? $data['pendidikan'] ?? null) ?? $warga->pendidikan_terakhir,
            'pekerjaan' => $data['pekerjaan'] ?? $warga->pekerjaan,
            'alamat' => $data['alamat'] ?? $warga->alamat,
            'status_hubungan' => Warga::normalisasiStatusHubungan($data['status_hubungan'] ?? $data['shdk'] ?? null) ?? $warga->status_hubungan,
            'status_warga' => $statusBaru,
        ];

        // Validasi dan pembaruan NIK jika diedit oleh admin
        $newNik = ! empty($data['nik']) ? trim($data['nik']) : null;
        if ($newNik && $newNik !== $warga->nik) {
            $nikSudahAda = Warga::where('nik', $newNik)->where('id', '!=', $warga->id)->exists();
            if ($nikSudahAda) {
                throw new InvalidArgumentException("NIK {$newNik} sudah digunakan oleh warga lain.");
            }

            $updateData['nik'] = $newNik;

            // Jika username akun login keluarga terikat dengan NIK lama, perbarui usernamenya
            if ($warga->user && $warga->user->username === $warga->nik) {
                $warga->user->update(['username' => $newNik]);
            }
        }

        $warga->update($updateData);

        // BR-02: Jika status_warga berubah keluar dari hidup -> jalankan transfer akun
        if ($statusLama === 'hidup' && in_array($statusBaru, ['pindah_kk', 'meninggal', 'pindah_rumah'])) {
            $warga->prosesTransferAkunOtomatis();
        }

        return $warga;
    }

    /**
     * Memperbarui status tempat tinggal dan status ekonomi Kartu Keluarga
     */
    public function ubahStatusKeluarga(Keluarga $keluarga, string $statusTinggal, string $statusEkonomi): Keluarga
    {
        $keluarga->update([
            'status_tinggal' => $statusTinggal,
            'status_ekonomi' => $statusEkonomi,
        ]);

        return $keluarga;
    }

    /**
     * Me-reset kata sandi akun keluarga menjadi NIK pemilik akun aktif (BR-04)
     *
     * @throws InvalidArgumentException
     */
    public function resetPasswordKeluarga(Keluarga $keluarga): User
    {
        $akun = $this->ambilAkunKeluarga($keluarga);

        if (! $akun) {
            throw new InvalidArgumentException('Akun login untuk Kartu Keluarga ini belum ditemukan.');
        }

        $akun->update([
            'password' => Hash::make($akun->username),
        ]);

        return $akun;
    }

    /**
     * Menghapus anggota keluarga (BR-02 & BR-03)
     *
     * @throws DomainException
     */
    public function hapusAnggota(Warga $warga): void
    {
        // Cegah menghapus jika kepala keluarga masih memiliki tanggungan anggota keluarga hidup lainnya
        if ($warga->status_hubungan === 'kepala_keluarga') {
            $anggotaLain = Warga::where('keluarga_id', $warga->keluarga_id)
                ->where('id', '!=', $warga->id)
                ->where('status_warga', 'hidup')
                ->count();

            if ($anggotaLain > 0) {
                throw new DomainException("Tidak dapat menghapus Kepala Keluarga ({$warga->nama}) karena masih ada {$anggotaLain} anggota keluarga lainnya. Ubah status hubungan kepala keluarga terlebih dahulu.");
            }
        }

        // Jalankan transfer akun jika yang dihapus adalah pemegang akun
        $warga->prosesTransferAkunOtomatis();

        $warga->delete();
    }

    /**
     * Menghapus seluruh Kartu Keluarga beserta data terkait
     */
    public function hapusKeluarga(Keluarga $keluarga): void
    {
        DB::transaction(function () use ($keluarga) {
            $akun = $this->ambilAkunKeluarga($keluarga);

            // Hapus akun warga jika bukan admin
            if ($akun && $akun->role !== 'admin') {
                $akun->delete();
            }

            // Hapus keluarga (cascade delete akan menghapus warga, iuran, dan riwayat terkait)
            $keluarga->delete();
        });
    }

    /**
     * Helper mengambil akun User yang berelasi dengan Kartu Keluarga
     */
    public function ambilAkunKeluarga(Keluarga $keluarga): ?User
    {
        return $keluarga->ambilAkunUser();
    }
}
