<?php

declare(strict_types=1);

namespace App\Enums;

enum MenuType: string
{
    case Link     = 'link';
    case Dropdown = 'dropdown';
    case Divider  = 'divider';
    case Label    = 'label';
    case Custom   = 'custom';

    public function toArray(): string
    {
        return match ($this) {
            self::Link     => 'Link',
            self::Dropdown => 'Dropdown',
            self::Divider  => 'Divider',
            self::Label    => 'Label',
            self::Custom   => 'Custom',
        };
    }
}
