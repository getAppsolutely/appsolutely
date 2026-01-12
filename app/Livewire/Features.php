<?php

declare(strict_types=1);

namespace App\Livewire;

final class Features extends GeneralBlock
{
    protected array $defaultDisplayOptions = [
        'title'        => '',
        'subtitle'     => '',
        'descriptions' => [
            '',
        ],
        'features' => [
            [
                'type'     => 'image',
                'url'      => 'assets/images/feature1.webp',
                'title'    => '',
                'subtitle' => '',
                'link'     => '',
            ],
            [
                'type'     => 'image',
                'url'      => 'assets/images/feature2.webp',
                'title'    => '',
                'subtitle' => '',
                'link'     => '',
            ],
            [
                'type'     => 'image',
                'url'      => 'assets/images/feature3.webp',
                'title'    => '',
                'subtitle' => '',
                'link'     => '',
            ],
        ],
    ];
}
