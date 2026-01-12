<?php

declare(strict_types=1);

namespace App\Livewire;

final class VideoShowcase extends GeneralBlock
{
    protected array $defaultDisplayOptions = [
        'video_url'             => 'assets/videos/your-video.mp4',
        'poster_image'          => 'assets/images/posters/your-poster.webp',
        'title'                 => 'Your Title Here',
        'subtitle'              => 'Your Subtitle Here',
        'description'           => 'Your descriptive text goes here, describing the content in a compelling way.',
        'cta_text'              => 'Call To Action',
        'cta_link'              => '/your/cta/link',
        'autoplay'              => true,
        'loop'                  => true,
        'muted'                 => true,
        'controls'              => false,
        'mobile_fallback_image' => 'assets/images/fallbacks/mobile-fallback.webp',
        'overlay_opacity'       => 0.4,
        'text_color'            => 'white',
        'text_position'         => 'center',
        'video_format'          => 'mp4',
        'fallback_videos'       => [
            [
                'url'    => 'assets/videos/fallbacks/your-video.webm',
                'format' => 'webm',
            ],
            [
                'url'    => 'assets/videos/fallbacks/your-video.mov',
                'format' => 'mov',
            ],
        ],
        'fallback_text' => 'Your browser does not support the video tag.',
    ];
}
