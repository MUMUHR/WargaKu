<?php

namespace App\Services;

use App\Models\Keluarga;
use App\Models\Riwayat_Perubahan;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Service Layer untuk Mengelola Alur Pengajuan & Verifikasi Perubahan Data (F006, F007, F008, UC-11)
 *
 * Bertanggung jawab menangani:
 * 1. Pembuatan draft pengajuan perubahan data oleh warga (ubah data & tambah anggota)
 * 2. Upload & manajemen berkas bukti dokumen
 * 3. Pembatalan pengajuan oleh warga
 * 4. Verifikasi (Persetujuan & Penolakan) oleh Admin RT secara transaksional
 */
class PengajuanPerubahanService
{
    /**
     * Peta label jenjang pendidikan untuk pemformatan data komparasi
     */
    public const MAP_PENDIDIKAN = [
        'tidak_belum_sekolah' => 'Tidak / Belum Sekolah',
        'belum_tamat_sd' => 'Belum Tamat SD / Sederajat',
        'sd_sederajat' => 'SD / Sederajat',
        'smp_sederajat' => 'SMP / Sederajat',
        'sma_sederajat' => 'SLTA / SMA / SMK / Sederajat',
        'd1_d2' => 'Diploma I / II',
        'd3' => 'Diploma III',
        'd4_s1' => 'Diploma IV / Strata 1 (S1)',
        's2' => 'Strata 2 (S2)',
        's3' => 'Strata 3 (S3)',
    ];

    /**
     * Membuat pengajuan ubah data kependudukan anggota keluarga (F006)
     */
    public function buatPengajuanUbahData(
        Keluarga $keluarga,
        Warga $warga,
        array $input,
        mixed $buktiFiles = null,
        ?string $alasan = null
    ): Riwayat_Perubahan {
        // 1. Deteksi perubahan antara data lama dan input baru
        [$dataLama, $dataBaru] = $this->deteksiPerubahanData($warga, $input, $keluarga);

        // 2. Upload berkas bukti
        $buktiPath = $this->uploadBerkasBukti($buktiFiles);

        // 3. Simpan ke tabel riwayat_perubahan
        return Riwayat_Perubahan::create([
            'keluarga_id' => $keluarga->id,
            'warga_id' => $warga->id,
            'jenis_pengajuan' => 'ubah_data',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'bukti_path' => $buktiPath,
            'status' => 'menunggu',
            'alasan' => $alasan ?: 'Pembaruan data kependudukan',
        ]);
    }

    /**
     * Membuat pengajuan pendaftaran anggota keluarga baru (F007)
     */
    public function buatPengajuanTambahAnggota(
        Keluarga $keluarga,
        array $input,
        mixed $buktiFiles = null
    ): Riwayat_Perubahan {
        $namaLengkap = $input['nama_lengkap'] ?? $input['nama'] ?? 'Anggota Baru';

        $dataBaru = [
            'nik' => ! empty($input['nik']) ? trim($input['nik']) : null,
            'nama' => $namaLengkap,
            'tempat_lahir' => $input['tempat_lahir'] ?? null,
            'tanggal_lahir' => $input['tanggal_lahir'] ?? null,
            'jenis_kelamin' => Warga::normalisasiJenisKelamin($input['jenis_kelamin'] ?? null) ?? 'laki_laki',
            'agama' => Warga::normalisasiAgama($input['agama'] ?? null),
            'pendidikan_terakhir' => Warga::normalisasiPendidikan($input['pendidikan_terakhir'] ?? null),
            'pekerjaan' => $input['pekerjaan'] ?? null,
            'alamat' => $input['alamat_domisili'] ?? $input['alamat'] ?? $keluarga->alamat,
            'status_hubungan' => Warga::normalisasiStatusHubungan($input['hubungan_keluarga'] ?? $input['status_hubungan'] ?? null) ?? 'anak',
            'status_warga' => 'hidup',
        ];

        $buktiPath = $this->uploadBerkasBukti($buktiFiles);

        return Riwayat_Perubahan::create([
            'keluarga_id' => $keluarga->id,
            'warga_id' => null,
            'jenis_pengajuan' => 'tambah_anggota',
            'data_lama' => null,
            'data_baru' => $dataBaru,
            'bukti_path' => $buktiPath,
            'status' => 'menunggu',
            'alasan' => "Pendaftaran anggota keluarga baru ({$namaLengkap})",
        ]);
    }

    /**
     * Membatalkan pengajuan perubahan data oleh warga jika masih berstatus 'menunggu'
     *
     * @throws InvalidArgumentException
     */
    public function batalkanPengajuan(Riwayat_Perubahan $pengajuan): bool
    {
        if ($pengajuan->status !== 'menunggu') {
            throw new InvalidArgumentException('Hanya pengajuan dengan status Menunggu Verifikasi yang dapat dibatalkan.');
        }

        $pengajuan->update([
            'status' => 'dibatalkan',
            'alasan' => trim(($pengajuan->alasan ? $pengajuan->alasan.' ' : '').'(Dibatalkan oleh pemohon)'),
        ]);

        return true;
    }

    /**
     * Memproses verifikasi pengajuan (Setujui atau Tolak) oleh Admin RT (F008, UC-11, BR-08)
     *
     * @throws InvalidArgumentException
     */
    public function verifikasiPengajuan(
        Riwayat_Perubahan $pengajuan,
        string $tindakan,
        ?string $alasan,
        int $adminId
    ): void {
        if ($pengajuan->status !== 'menunggu') {
            throw new InvalidArgumentException("Pengajuan ini tidak dapat diproses karena berstatus: " . ucfirst($pengajuan->status) . ".");
        }

        if ($tindakan === 'setujui') {
            DB::transaction(function () use ($pengajuan, $alasan, $adminId) {
                // Skenario A: Ubah Data Warga Eksisting
                if ($pengajuan->jenis_pengajuan === 'ubah_data' || $pengajuan->warga_id) {
                    $warga = Warga::find($pengajuan->warga_id);
                    if ($warga && ! empty($pengajuan->data_baru)) {
                        $statusLama = $warga->status_warga;

                        $fillableData = collect($pengajuan->data_baru)
                            ->only([
                                'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
                                'agama', 'pendidikan_terakhir', 'pekerjaan', 'alamat',
                                'status_hubungan', 'status_warga',
                            ])->toArray();

                        // Normalisasi format enum
                        if (isset($fillableData['jenis_kelamin'])) {
                            $fillableData['jenis_kelamin'] = Warga::normalisasiJenisKelamin($fillableData['jenis_kelamin']);
                        }
                        if (isset($fillableData['status_hubungan'])) {
                            $fillableData['status_hubungan'] = Warga::normalisasiStatusHubungan($fillableData['status_hubungan']);
                        }
                        if (isset($fillableData['agama'])) {
                            $fillableData['agama'] = Warga::normalisasiAgama($fillableData['agama']);
                        }
                        if (isset($fillableData['pendidikan_terakhir'])) {
                            $fillableData['pendidikan_terakhir'] = Warga::normalisasiPendidikan($fillableData['pendidikan_terakhir']);
                        }
                        if (isset($fillableData['status_warga'])) {
                            $fillableData['status_warga'] = Warga::normalisasiStatusWarga($fillableData['status_warga']);
                        }

                        $warga->update($fillableData);

                        // Trigger BR-02 jika status_warga keluar dari hidup
                        if ($statusLama === 'hidup' && in_array($warga->status_warga, ['pindah_kk', 'meninggal', 'pindah_rumah'])) {
                            $warga->prosesTransferAkunOtomatis();
                        }
                    }
                }
                // Skenario B: Tambah Anggota Keluarga Baru
                elseif ($pengajuan->jenis_pengajuan === 'tambah_anggota' && ! empty($pengajuan->data_baru)) {
                    $keluarga = $pengajuan->keluarga ?? Keluarga::find($pengajuan->keluarga_id);
                    $akunKk = $keluarga?->ambilAkunUser();
                    $dataBaru = $pengajuan->data_baru;

                    $nikBaru = $dataBaru['nik'] ?? null;
                    if ($nikBaru) {
                        $existing = Warga::where('nik', $nikBaru)->first();

                        if ($existing && $existing->status_warga === 'pindah_kk') {
                            // Pindahkan warga pindah_kk ke KK baru & hidupkan kembali
                            $existing->update([
                                'keluarga_id' => $keluarga->id,
                                'user_id' => $akunKk?->id,
                                'nama' => $dataBaru['nama'] ?? $existing->nama,
                                'tempat_lahir' => $dataBaru['tempat_lahir'] ?? $existing->tempat_lahir,
                                'tanggal_lahir' => $dataBaru['tanggal_lahir'] ?? $existing->tanggal_lahir,
                                'jenis_kelamin' => Warga::normalisasiJenisKelamin($dataBaru['jenis_kelamin'] ?? null) ?? $existing->jenis_kelamin,
                                'agama' => Warga::normalisasiAgama($dataBaru['agama'] ?? null) ?? $existing->agama,
                                'pendidikan_terakhir' => Warga::normalisasiPendidikan($dataBaru['pendidikan_terakhir'] ?? null) ?? $existing->pendidikan_terakhir,
                                'pekerjaan' => $dataBaru['pekerjaan'] ?? $existing->pekerjaan,
                                'alamat' => $dataBaru['alamat'] ?? ($keluarga?->alamat ?? 'RT 04'),
                                'status_hubungan' => Warga::normalisasiStatusHubungan($dataBaru['status_hubungan'] ?? null) ?? $existing->status_hubungan,
                                'status_warga' => 'hidup',
                            ]);
                        } elseif (! $existing) {
                            Warga::create([
                                'keluarga_id' => $pengajuan->keluarga_id,
                                'user_id' => $akunKk?->id,
                                'nik' => $nikBaru,
                                'nama' => $dataBaru['nama'] ?? 'Anggota Baru',
                                'tempat_lahir' => $dataBaru['tempat_lahir'] ?? null,
                                'tanggal_lahir' => $dataBaru['tanggal_lahir'] ?? null,
                                'jenis_kelamin' => Warga::normalisasiJenisKelamin($dataBaru['jenis_kelamin'] ?? null) ?? 'laki_laki',
                                'agama' => Warga::normalisasiAgama($dataBaru['agama'] ?? null),
                                'pendidikan_terakhir' => Warga::normalisasiPendidikan($dataBaru['pendidikan_terakhir'] ?? null),
                                'pekerjaan' => $dataBaru['pekerjaan'] ?? null,
                                'alamat' => $dataBaru['alamat'] ?? ($keluarga?->alamat ?? 'RT 04'),
                                'status_hubungan' => Warga::normalisasiStatusHubungan($dataBaru['status_hubungan'] ?? null) ?? 'anak',
                                'status_warga' => 'hidup',
                            ]);
                        }
                    }
                }

                $pengajuan->update([
                    'status' => 'disetujui',
                    'admin_id' => $adminId,
                    'alasan' => $alasan ?: 'Disetujui oleh Pengurus RT.',
                ]);
            });
        } elseif ($tindakan === 'tolak') {
            $pengajuan->update([
                'status' => 'ditolak',
                'admin_id' => $adminId,
                'alasan' => $alasan ?: 'Pengajuan ditolak oleh Pengurus RT.',
            ]);
        } else {
            throw new InvalidArgumentException("Tindakan verifikasi tidak valid: {$tindakan}.");
        }
    }

    /**
     * Membandingkan data database dengan input form untuk menemukan perbedaan riil
     */
    public function deteksiPerubahanData(Warga $warga, array|Request $input, Keluarga $keluarga): array
    {
        $inputData = $input instanceof Request ? $input->all() : $input;

        $fieldMap = [
            'new_nama' => 'nama',
            'new_tempat_lahir' => 'tempat_lahir',
            'new_tgl_lahir' => 'tanggal_lahir',
            'new_jk' => 'jenis_kelamin',
            'new_agama' => 'agama',
            'new_pendidikan' => 'pendidikan_terakhir',
            'new_pekerjaan' => 'pekerjaan',
            'new_alamat' => 'alamat',
            'new_shdk' => 'status_hubungan',
            'new_status_warga' => 'status_warga',
        ];

        $dataLama = [];
        $dataBaru = [];

        foreach ($fieldMap as $inputKey => $kolom) {
            $oldValue = $this->formatNilaiPerubahan($kolom, $warga->$kolom ?? ($kolom === 'alamat' ? $keluarga->alamat : ''));
            $newValue = $this->formatNilaiPerubahan($kolom, $inputData[$inputKey] ?? $warga->$kolom);

            if ($oldValue !== $newValue && $newValue !== '') {
                $dataLama[$kolom] = $oldValue;
                $dataBaru[$kolom] = $newValue;
            }
        }

        // Fallback jika tidak ada perubahan terdeteksi
        if (empty($dataBaru)) {
            $dataLama['pekerjaan'] = $warga->pekerjaan ?? '-';
            $dataBaru['pekerjaan'] = $inputData['new_pekerjaan'] ?? ($warga->pekerjaan ?? '-');
        }

        return [$dataLama, $dataBaru];
    }

    /**
     * Normalisasi format nilai untuk komparasi snapshot
     */
    protected function formatNilaiPerubahan(string $kolom, mixed $nilai): string
    {
        $val = trim((string) $nilai);

        return match ($kolom) {
            'tanggal_lahir' => $val ? Carbon::parse($val)->format('Y-m-d') : '',
            'jenis_kelamin' => (string) Warga::normalisasiJenisKelamin($val),
            'agama' => (string) Warga::normalisasiAgama($val),
            'status_hubungan' => (string) Warga::normalisasiStatusHubungan($val),
            'status_warga' => (string) Warga::normalisasiStatusWarga($val),
            'pendidikan_terakhir' => (string) (self::MAP_PENDIDIKAN[Warga::normalisasiPendidikan($val)] ?? self::MAP_PENDIDIKAN[$val] ?? $val),
            'pekerjaan', 'alamat' => $val === '' ? '-' : $val,
            default => $val,
        };
    }

    /**
     * Mengunggah berkas bukti pendukung (mendukung 1 berkas maupun banyak berkas)
     */
    public function uploadBerkasBukti(mixed $bukti): string
    {
        if (! $bukti) {
            return '';
        }

        // Tangani jika input adalah objek Request
        if ($bukti instanceof Request) {
            if (! $bukti->hasFile('bukti')) {
                return '';
            }
            $files = $bukti->file('bukti');
        } else {
            $files = $bukti;
        }

        if (! is_array($files)) {
            $files = [$files];
        }

        $targetDir = public_path('uploads/bukti');
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $buktiPaths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $ext = $file->getClientOriginalExtension();
                $namaBerkas = 'bukti_' . time() . '_' . uniqid() . '.' . $ext;
                $file->move($targetDir, $namaBerkas);
                $buktiPaths[] = 'uploads/bukti/' . $namaBerkas;
            }
        }

        return match (count($buktiPaths)) {
            0 => '',
            1 => $buktiPaths[0],
            default => json_encode($buktiPaths),
        };
    }
}
