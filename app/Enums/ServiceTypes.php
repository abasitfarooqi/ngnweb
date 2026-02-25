<?php

namespace App\Enums;

enum ServiceTypes: string
{
    case BasicService = 'Basic Service';
    case FullService = 'Full Service';

    public function label(): string
    {
        return match ($this) {
            self::BasicService => 'Basic Service',
            self::FullService => 'Full Service',
        };
    }
}
