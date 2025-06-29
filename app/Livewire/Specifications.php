<?php

declare(strict_types=1);

namespace App\Livewire;

final class Specifications extends BaseBlock
{
    protected array $defaultDisplayOptions = [
        'title'          => 'Item Specifications',
        'subtitle'       => 'Overview of Key Features & Details',
        'description'    => 'Explore the core specifications and visual highlights of this product offering.',
        'layout'         => 'table',
        'columns'        => 2,
        'specifications' => [
            [
                'label' => 'Front Exterior',
                'value' => 'Modern design with signature grille',
            ],
            [
                'label' => 'Dashboard Interior',
                'value' => 'Premium materials with digital display',
            ],
        ],
    ];
}
