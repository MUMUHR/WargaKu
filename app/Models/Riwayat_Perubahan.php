<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Riwayat_Perubahan extends Model
{
    use HasFactory;

    protected $table = 'riwayat__perubahans';

    protected $fillable = [
        'warga_id',
        'keluarga_id',
        'jenis_pengajuan',
        'data_lama',
        'data_baru',
        'bukti_path',
        'status',
        'alasan',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
        ];
    }

    /**
     * Warga yang diajukan perubahannya (null jika tambah_anggota)
     */
    public function warga()
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    /**
     * Admin RT yang memverifikasi pengajuan
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Kartu Keluarga asal pengajuan
     */
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id');
    }
}
