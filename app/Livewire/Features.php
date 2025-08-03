<?php

declare(strict_types=1);

namespace App\Livewire;

final class Features extends BaseBlock
{
    protected array $defaultDisplayOptions = [
        'style'        => 'default',
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
