<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kk',
        'status_ekonomi',
        'status_tinggal'
    ];

    public function wargas() {
        return $this->hasMany(Warga::class, 'keluarga_id');
    }

    public function kepalaKeluarga() {
        return $this->hasOne(Warga::class, 'keluarga_id')
            ->where('status_hubungan', 'kepala_keluarga');
    }

    public function iurans() {
        return $this->hasMany(Iuran::class, 'keluarga_id');
    }

    public function riwayatPerubahan() {
        return $this->hasMany(Riwayat_Perubahan::class, 'keluarga_id');
    }

    /**
     * Mengambil akun User yang terikat pada Kartu Keluarga ini.
     * Menggunakan 3 prioritas:
     * 1. Akun user_id milik kepala keluarga
     * 2. Akun user_id milik salah satu anggota keluarga hidup
     * 3. Akun dengan username = NIK kepala keluarga
     */
    public function ambilAkunUser(): ?User
    {
        // 1. Cek user_id dari kepala keluarga
        if ($this->kepalaKeluarga && $this->kepalaKeluarga->user_id) {
            $user = User::find($this->kepalaKeluarga->user_id);
            if ($user) {
                return $user;
            }
        }

        // 2. Cek user_id dari anggota keluarga mana pun
        $wargaWithUser = $this->wargas()->whereNotNull('user_id')->first();
        if ($wargaWithUser && $wargaWithUser->user_id) {
            $user = User::find($wargaWithUser->user_id);
            if ($user) {
                return $user;
            }
        }

        // 3. Cek User berdasarkan username = NIK kepala keluarga
        if ($this->kepalaKeluarga && $this->kepalaKeluarga->nik) {
            $user = User::where('username', $this->kepalaKeluarga->nik)->first();
            if ($user) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Accessor untuk alamat domisili keluarga:
     * Mengambil alamat Kepala Keluarga hidup, atau anggota hidup lainnya, atau default RT 04.
     */
    public function getAlamatAttribute(): string
    {
        $kepala = $this->wargas()->where('status_hubungan', 'kepala_keluarga')->where('status_warga', 'hidup')->first()
            ?? $this->kepalaKeluarga;

        if ($kepala && ! empty($kepala->alamat)) {
            return $kepala->alamat;
        }

        $anggotaLain = $this->wargas()->where('status_warga', 'hidup')->whereNotNull('alamat')->first();
        if ($anggotaLain && ! empty($anggotaLain->alamat)) {
            return $anggotaLain->alamat;
        }

        return 'RT 04';
    }
}
