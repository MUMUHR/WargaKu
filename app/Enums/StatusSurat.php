<?php

namespace App\Enums;

enum StatusSurat: string
{
    case MENUNGGU = 'menunggu';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case DIBATALKAN = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU => 'Menunggu Verifikasi',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::DIBATALKAN => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::MENUNGGU => 'badge-warning',
            self::DISETUJUI => 'badge-success',
            self::DITOLAK => 'badge-danger',
            self::DIBATALKAN => 'badge-secondary',
        };
    }
}
