<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'no_telp',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke warga (Akun KK bersama menaungi anggota KK)
     */
    public function wargas()
    {
        return $this->hasMany(Warga::class, 'user_id');
    }

    /**
     * Warga pemilik akun (Warga yang NIK-nya sama dengan username akun)
     */
    public function wargaPemilik()
    {
        return $this->hasOne(Warga::class, 'nik', 'username');
    }

    /**
     * Relasi ke Keluarga lewat tabel Warga
     */
    public function keluarga()
    {
        return $this->hasOneThrough(Keluarga::class, Warga::class, 'user_id', 'id', 'id', 'keluarga_id');
    }

    /**
     * Pengumuman yang diterbitkan oleh Admin RT ini
     */
    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'admin_id');
    }

    /**
     * Riwayat pengajuan perubahan data yang diverifikasi oleh Admin RT ini
     */
    public function verifikasiRiwayat()
    {
        return $this->hasMany(Riwayat_Perubahan::class, 'admin_id');
    }

    /**
     * Helper cek role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }
}
