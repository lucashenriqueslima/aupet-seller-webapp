<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PetType: string implements HasLabel
{
    case Dog = 'dog';
    case Cat = 'cat';

    public function getLabel(): string
    {
        return match ($this) {
            self::Dog => 'Cachorro',
            self::Cat => 'Gato',
        };
    }
}
