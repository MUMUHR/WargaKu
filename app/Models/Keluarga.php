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

}
