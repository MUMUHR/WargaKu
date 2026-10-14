<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;
    protected $table = 'pengumumen';


    protected $fillable = [
        'judul',
        'isi',
        'gambar',
        'status_publikasi',
        'admin_id'
    ];

    /**
     * Admin RT yang menerbitkan pengumuman
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Cast attributes.
     */
    protected function casts(): array
    {
        return [
            'status_publikasi' => \App\Enums\StatusPublikasi::class,
        ];
    }
}
