<?php

namespace App\Enums;

enum StatusIuran: string
{
    case LUNAS = 'lunas';
    case BELUM_LUNAS = 'belum_lunas';

    public function label(): string
    {
        return match ($this) {
            self::LUNAS => 'Lunas',
            self::BELUM_LUNAS => 'Belum Lunas',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LUNAS => 'badge-success',
            self::BELUM_LUNAS => 'badge-danger',
        };
    }
}
