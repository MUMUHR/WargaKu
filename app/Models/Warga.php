<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'keluarga_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pendidikan_terakhir',
        'alamat',
        'pekerjaan',
        'status_hubungan',
        'status_warga',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Relasi ke Kartu Keluarga
     */
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id');
    }

    /**
     * Akun KK bersama (nullable)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Surat yang dimohonkan oleh warga ini
     */
    public function suratPemohon()
    {
        return $this->hasMany(Surat::class, 'pemohon_id');
    }

    /**
     * Surat yang ditujukan untuk warga ini
     */
    public function suratTujuan()
    {
        return $this->hasMany(Surat::class, 'warga_tujuan_id');
    }

    /**
     * Pengajuan riwayat perubahan data atas warga ini
     */
    public function riwayatPerubahan()
    {
        return $this->hasMany(Riwayat_Perubahan::class, 'warga_id');
    }

    /**
     * Scope untuk mengurutkan anggota keluarga sesuai hierarki resmi Kartu Keluarga:
     * 1. Kepala Keluarga, 2. Suami, 3. Istri, 4. Anak, 5. Menantu, 6. Cucu, 7. Orang Tua, 8. Mertua, 9. Famili Lain, 10. Lainnya.
     * Lalu diurutkan berdasarkan tanggal lahir tertua ke termuda.
     */
    public function scopeUrutHierarkiKk($query)
    {
        return $query->orderByRaw("CASE 
            WHEN status_hubungan = 'kepala_keluarga' THEN 1 
            WHEN status_hubungan = 'suami' THEN 2 
            WHEN status_hubungan = 'istri' THEN 3 
            WHEN status_hubungan = 'anak' THEN 4 
            WHEN status_hubungan = 'menantu' THEN 5 
            WHEN status_hubungan = 'cucu' THEN 6 
            WHEN status_hubungan = 'orang_tua' THEN 7 
            WHEN status_hubungan = 'mertua' THEN 8 
            WHEN status_hubungan = 'famili_lain' THEN 9 
            ELSE 10 
        END ASC")->orderBy('tanggal_lahir', 'asc');
    }

    /**
     * Business Rule BR-02 (Transfer Akun Otomatis) & BR-03 (Pembersihan Akun).
     * Dijalankan saat status warga berubah keluar dari hidup (meninggal/pindah_kk/pindah_rumah) atau dihapus.
     */
    public function prosesTransferAkunOtomatis(): void
    {
        $akunLama = $this->user;
        $keluargaId = $this->keluarga_id;

        // 1. Lepas asosiasi user_id warga ini
        $this->update(['user_id' => null]);

        if (! $akunLama) {
            return;
        }

        // 2. Cek apakah warga ini adalah pemilik akun (username == nik)
        if ($akunLama->username !== $this->nik) {
            // Bukan pemilik akun, akun KK tetap aktif untuk pemilik saat ini
            return;
        }

        // 3. Cek apakah masih ada anggota berstatus 'hidup' pada keluarga yang sama
        $anggotaHidup = self::where('keluarga_id', $keluargaId)
            ->where('status_warga', 'hidup')
            ->where('id', '!=', $this->id)
            ->get();

        if ($anggotaHidup->isEmpty()) {
            // BR-03: Seluruh anggota KK habis -> hapus akun users
            $akunLama->delete();
            return;
        }

        // Urutan hierarki pemilihan pemilik akun baru:
        // a. Kepala Keluarga
        $pemilikBaru = $anggotaHidup->firstWhere('status_hubungan', 'kepala_keluarga');

        // b. Istri / Suami
        if (! $pemilikBaru) {
            $pemilikBaru = $anggotaHidup->first(fn ($w) => in_array($w->status_hubungan, ['istri', 'suami']));
        }

        // c. Anak Tertua usia 17+
        if (! $pemilikBaru) {
            $pemilikBaru = $anggotaHidup
                ->filter(function ($w) {
                    if ($w->status_hubungan !== 'anak' || empty($w->tanggal_lahir)) {
                        return false;
                    }
                    return Carbon::parse($w->tanggal_lahir)->age >= 17;
                })
                ->sortBy('tanggal_lahir')
                ->first();
        }

        // d. Anggota Tertua
        if (! $pemilikBaru) {
            $pemilikBaru = $anggotaHidup->sortBy('tanggal_lahir')->first();
        }

        if ($pemilikBaru) {
            // Perbarui username ke NIK pemilik baru, password tetap
            $akunLama->update([
                'username' => $pemilikBaru->nik,
            ]);

            // Pastikan user_id mengarah ke akun KK
            $pemilikBaru->update([
                'user_id' => $akunLama->id,
            ]);
        }
    }

    /**
     * Helper normalisasi Jenis Kelamin
     */
    public static function normalisasiJenisKelamin(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim($value));
        if (in_array($v, ['laki_laki', 'laki-laki', 'laki laki', 'pria', 'l'])) {
            return 'laki_laki';
        }
        if (in_array($v, ['perempuan', 'wanita', 'p'])) {
            return 'perempuan';
        }

        return 'laki_laki';
    }

    /**
     * Helper normalisasi Status Hubungan Dalam Keluarga (SHDK)
     */
    public static function normalisasiStatusHubungan(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim(str_replace(['-', ' '], '_', $value)));
        $allowed = [
            'kepala_keluarga', 'suami', 'istri', 'anak', 'menantu',
            'cucu', 'orang_tua', 'mertua', 'famili_lain', 'lainnya',
        ];

        if (in_array($v, $allowed)) {
            return $v;
        }

        return match ($v) {
            'anak_kandung' => 'anak',
            'kepala' => 'kepala_keluarga',
            'famili' => 'famili_lain',
            default => 'lainnya',
        };
    }

    /**
     * Helper normalisasi Agama
     */
    public static function normalisasiAgama(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim($value));
        $allowed = ['islam', 'katolik', 'protestan', 'buddha', 'hindu', 'konghucu'];

        if (in_array($v, $allowed)) {
            return $v;
        }

        if (str_contains($v, 'kristen') || str_contains($v, 'protestan')) {
            return 'protestan';
        }
        if (str_contains($v, 'katolik')) {
            return 'katolik';
        }

        return 'islam';
    }

    /**
     * Daftar Standar Opsi Pendidikan Terakhir
     */
    public const DAFTAR_PENDIDIKAN = [
        'tidak_belum_sekolah' => 'Tidak / Belum Sekolah',
        'belum_tamat_sd'      => 'Belum Tamat SD / Sederajat',
        'sd_sederajat'        => 'SD / Sederajat',
        'smp_sederajat'       => 'SMP / Sederajat',
        'sma_sederajat'       => 'SLTA / SMA / SMK / Sederajat',
        'd1_d2'               => 'Diploma I / II',
        'd3'                  => 'Diploma III',
        'd4_s1'               => 'Diploma IV / Strata 1 (S1)',
        's2'                  => 'Strata 2 (S2)',
        's3'                  => 'Strata 3 (S3)',
    ];

    /**
     * Helper normalisasi Pendidikan Terakhir
     */
    public static function normalisasiPendidikan(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim(str_replace(['/', '-', ' ', '(', ')'], '_', $value)));
        $allowed = [
            'tidak_belum_sekolah', 'belum_tamat_sd', 'sd_sederajat',
            'smp_sederajat', 'sma_sederajat', 'd1_d2', 'd3', 'd4_s1', 's2', 's3',
        ];

        if (in_array($v, $allowed)) {
            return $v;
        }

        if (str_contains($v, 's3') || str_contains($v, 'doktor')) {
            return 's3';
        }
        if (str_contains($v, 's2') || str_contains($v, 'magister')) {
            return 's2';
        }
        if (str_contains($v, 'd4_s1') || str_contains($v, 's1') || str_contains($v, 'sarjana') || str_contains($v, 'd4')) {
            return 'd4_s1';
        }
        if (str_contains($v, 'd3') || str_contains($v, 'diploma_3') || str_contains($v, 'diploma_iii')) {
            return 'd3';
        }
        if (str_contains($v, 'd1_d2') || str_contains($v, 'd1') || str_contains($v, 'd2') || str_contains($v, 'diploma_i') || str_contains($v, 'diploma_ii')) {
            return 'd1_d2';
        }
        if (str_contains($v, 'sma') || str_contains($v, 'smk') || str_contains($v, 'slta')) {
            return 'sma_sederajat';
        }
        if (str_contains($v, 'smp') || str_contains($v, 'sltp')) {
            return 'smp_sederajat';
        }
        if (str_contains($v, 'belum_tamat') || str_contains($v, 'belum_sd')) {
            return 'belum_tamat_sd';
        }
        if (str_contains($v, 'tidak') || str_contains($v, 'belum_sekolah')) {
            return 'tidak_belum_sekolah';
        }
        if (str_contains($v, 'sd')) {
            return 'sd_sederajat';
        }

        return 'tidak_belum_sekolah';
    }

    /**
     * Helper normalisasi Status Warga
     */
    public static function normalisasiStatusWarga(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $v = strtolower(trim(str_replace([' ', '-', '(', ')'], '_', $value)));
        if (str_contains($v, 'hidup')) {
            return 'hidup';
        }
        if (str_contains($v, 'pindah_kk')) {
            return 'pindah_kk';
        }
        if (str_contains($v, 'pindah_rumah') || str_contains($v, 'pindah')) {
            return 'pindah_rumah';
        }
        if (str_contains($v, 'meninggal')) {
            return 'meninggal';
        }

        return 'hidup';
    }
}
