<?php

namespace App\Models;

use App\Enums\StatusSurat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surats';

    protected $fillable = [
        'pemohon_id',
        'warga_tujuan_id',
        'keperluan',
        'nomor_surat',
        'status',
        'alasan',
    ];

    protected $casts = [
        'status' => StatusSurat::class,
    ];

    /**
     * Warga pemohon surat (pemilik akun KK)
     */
    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'pemohon_id');
    }

    /**
     * Warga anggota keluarga yang dituju oleh surat
     */
    public function wargaTujuan(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_tujuan_id');
    }

    /**
     * Status checkers
     */
    public function isMenunggu(): bool
    {
        return $this->status === StatusSurat::MENUNGGU || $this->status?->value === 'menunggu';
    }

    public function isDisetujui(): bool
    {
        return $this->status === StatusSurat::DISETUJUI || $this->status?->value === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === StatusSurat::DITOLAK || $this->status?->value === 'ditolak';
    }

    public function isDibatalkan(): bool
    {
        return $this->status === StatusSurat::DIBATALKAN || $this->status?->value === 'dibatalkan';
    }

    /**
     * BR-05: Penomoran Surat
     * Format NNN/SP/RT04/RW08/{bulan_romawi}/{tahun}.
     * NNN sekuensial dan reset otomatis setiap pergantian tahun kalender.
     */
    public static function generateNomorSurat(?int $tahun = null, ?int $bulan = null): string
    {
        $tahun = $tahun ?? (int) now()->format('Y');
        $bulan = $bulan ?? (int) now()->format('n');

        $bulanRomawiList = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $bulanRomawi = $bulanRomawiList[$bulan] ?? 'I';

        // Hitung atau cari nomor urut tertinggi pada tahun kalender yang bersangkutan
        $suratTahunIni = self::whereYear('created_at', $tahun)
            ->whereNotNull('nomor_surat')
            ->pluck('nomor_surat');

        $maxUrutan = 0;
        foreach ($suratTahunIni as $no) {
            if (preg_match('/^(\d+)\/SP\//i', $no, $m)) {
                $num = (int) $m[1];
                if ($num > $maxUrutan) {
                    $maxUrutan = $num;
                }
            }
        }

        $nextUrutan = $maxUrutan + 1;
        $nomorUrutPad = str_pad((string) $nextUrutan, 3, '0', STR_PAD_LEFT);

        return "{$nomorUrutPad}/SP/RT04/RW08/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Scope untuk mengurutkan permohonan surat:
     * 1. Status 'menunggu' paling atas (verifikasi prioritas Admin),
     * 2. Status 'disetujui',
     * 3. Status lainnya ('ditolak', 'dibatalkan'),
     * lalu berdasarkan rilis terbaru (latest id).
     */
    public function scopeUrutPrioritasStatus($query)
    {
        return $query->orderByRaw("CASE WHEN status = 'menunggu' THEN 1 WHEN status = 'disetujui' THEN 2 ELSE 3 END")
            ->latest('id');
    }
}
