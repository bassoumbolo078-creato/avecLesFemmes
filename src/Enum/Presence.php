<?php

namespace App\Enum;

enum Presence: string
{
    case PRESENT = 'present';
    case ABSENT_REPRESENTE = 'absent_represente';
    case ABSENT = 'absent';

    public function label(): string
    {
        return match ($this) {
            self::PRESENT => 'Présent',
            self::ABSENT_REPRESENTE => 'Absent représenté',
            self::ABSENT => 'Absent',
        };
    }
}