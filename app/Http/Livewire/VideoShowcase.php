<?php

declare(strict_types=1);

namespace App\Http\Livewire;

final class VideoShowcase extends BaseBlock
{
    protected function defaultConfig(): array
    {
        return [
            'video_url'             => '',
            'poster_image'          => '',
            'title'                 => '',
            'subtitle'              => '',
            'description'           => '',
            'cta_text'              => '',
            'cta_link'              => '',
            'autoplay'              => true,
            'loop'                  => true,
            'muted'                 => true,
            'controls'              => false,
            'mobile_fallback_image' => '',
            'overlay_opacity'       => 0.3,
            'text_color'            => 'white',
            'text_position'         => 'center', // center, left, right
            'video_format'          => 'mp4', // mp4, webm, mov
            'fallback_videos'       => [], // Array of alternative video formats
        ];
    }
}
