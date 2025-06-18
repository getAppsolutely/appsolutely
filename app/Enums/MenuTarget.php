<?php

namespace App\Enums;

enum MenuTarget: string
{
    case Self   = '_self';
    case Blank  = '_blank';
    case Parent = '_parent';
    case Top    = '_top';
    case Modal  = 'modal';
    case Iframe = 'iframe';

    public function toArray(): string
    {
        return match ($this) {
            self::Self   => 'Same Tab (_self)',
            self::Blank  => 'New Tab (_blank)',
            self::Parent => 'Parent Frame (_parent)',
            self::Top    => 'Top Frame (_top)',
            self::Modal  => 'Open in Modal',
            self::Iframe => 'Load in Iframe',
        };
    }
}
