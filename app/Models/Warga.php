<?php

namespace App\Models;

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
     * 1. Kepala Keluarga, 2. Suami, 3. Istri, 4. Anak (tertua ke termuda), 5. Lainnya.
     */
    public function scopeUrutHierarkiKk($query)
    {
        return $query->orderByRaw("CASE 
            WHEN status_hubungan = 'kepala_keluarga' THEN 1 
            WHEN status_hubungan = 'suami' THEN 2 
            WHEN status_hubungan = 'istri' THEN 3 
            WHEN status_hubungan = 'anak' THEN 4 
            ELSE 5 
        END ASC")->orderBy('tanggal_lahir', 'asc');
    }
}
