<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class VideoShowcase extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $videoShowcase = [];

    /**
     * Mount the component with video showcase data.
     *
     * @param  array<string, mixed>  $videoShowcase
     */
    public function mount(array $videoShowcase = []): void
    {
        $this->videoShowcase = array_merge($this->defaultConfig(), $videoShowcase);
    }

    /**
     * Get default video showcase configuration.
     *
     * @return array<string, mixed>
     */
    private function defaultConfig(): array
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

    public function render(): object
    {
        return themed_view('livewire.video-showcase');
    }
}
