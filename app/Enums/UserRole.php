<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case WARGA = 'warga';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin RT',
            self::WARGA => 'Warga',
        };
    }
}
