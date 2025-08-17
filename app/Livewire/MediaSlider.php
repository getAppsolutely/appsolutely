<?php

declare(strict_types=1);

namespace App\Livewire;

final class MediaSlider extends BaseBlock
{
    protected array $defaultDisplayOptions = [
        'style'       => 'carousel', // carousel, fullscreen, or simple
        'title'       => 'Media Slider',
        'subtitle'    => 'Media Slider Subtitle',
        'description' => [
            'Media Slider Description 1',
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
        'show_controls'   => true,
        'show_indicators' => true,
        'autoplay'        => true,
    ];
}
