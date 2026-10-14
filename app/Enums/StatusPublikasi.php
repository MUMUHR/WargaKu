<?php

namespace App\Enums;

enum StatusPublikasi: string
{
    case DRAFT = 'draft';
    case PUBLISH = 'publish';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::PUBLISH => 'Diterbitkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'badge-secondary',
            self::PUBLISH => 'badge-success',
        };
    }
}
