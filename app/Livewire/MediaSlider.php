<?php

declare(strict_types=1);

namespace App\Livewire;

final class MediaSlider extends GeneralBlock
{
    protected array $defaultDisplayOptions = [
        'title'       => 'Media Slider',
        'subtitle'    => 'Media Slider Subtitle',
        'description' => [
            'Media Slider Description',
        ],
        'slides' => [
            [
                'type'     => 'image', // image or video
                'link'     => '/',
                'url'      => 'assets/images/slides/slide-1.webp',
                'title'    => 'Slide Title 1',
                'subtitle' => 'Slide Subtitle 1',
            ],
            [
                'type'     => 'image',
                'link'     => '/',
                'url'      => 'assets/images/slides/slide-2.webp',
                'title'    => 'Slide Title 2',
                'subtitle' => 'Slide Subtitle 2',
            ],
        ],
        'vehicles'        => [],
        'show_controls'   => true,
        'show_indicators' => true,
        'autoplay'        => true,
    ];
}
