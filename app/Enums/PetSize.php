<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PetSize: string implements HasLabel
{
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';

    public function getLabel(): string
    {
        return match ($this) {
            self::Small => 'Pequeno',
            self::Medium => 'Médio',
            self::Large => 'Grande',
        };
    }
}
