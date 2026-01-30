<?php

declare(strict_types=1);

namespace App\Enums;

enum FormEntrySpamStatus: int
{
    case Valid = 0;
    case Spam  = 1;

    public function label(): string
    {
        return match ($this) {
            self::Valid => 'Valid',
            self::Spam  => 'Spam',
        };
    }

    public function isSpam(): bool
    {
        return $this === self::Spam;
    }

    public static function toArray(): array
    {
        return [
            self::Valid->value => self::Valid->label(),
            self::Spam->value  => self::Spam->label(),
        ];
    }
}
