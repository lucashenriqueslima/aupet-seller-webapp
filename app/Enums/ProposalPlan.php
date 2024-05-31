<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ProposalPlan: string implements HasLabel
{
    case BasicJunior = 'basic_junior';
    case BasicSenior = 'basic_senior';
    case MediumJunior = 'medium_junior';
    case MediumSenior = 'medium_senior';
    case Premium = 'premium';
    public function getLabel(): string
    {
        return match ($this) {
            self::BasicJunior => 'Vital Junior',
            self::BasicSenior => 'Vital Senior',
            self::MediumJunior => 'Exclusivo Junior',
            self::MediumSenior => 'Exclusivo Senior',
            self::Premium => 'Supremo',
        };
    }

    public function getPrice(): float
    {
        return match ($this) {
            self::BasicJunior => 79.90,
            self::BasicSenior => 110.90,
            self::MediumJunior => 159.90,
            self::MediumSenior => 190.00,
            self::Premium => 250.00,
        };
    }
}
