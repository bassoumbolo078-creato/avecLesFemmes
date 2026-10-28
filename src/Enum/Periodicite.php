<?php

namespace App\Enum;

enum Periodicite: string
{
    case HEBDOMADAIRE = 'hebdomadaire';
    case BIMENSUEL = 'bimensuel';
    case QUINZAINE = 'quinzaine';
    case MENSUEL = 'mensuel';

    public function label(): string
    {
        return match ($this) {
            self::HEBDOMADAIRE => 'Hebdomadaire',
            self::BIMENSUEL => 'Bimensuel',
            self::QUINZAINE => 'Par quinzaine',
            self::MENSUEL => 'Mensuel',
        };
    }
}