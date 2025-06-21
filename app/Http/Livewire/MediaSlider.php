<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class MediaSlider extends Component
{
    /**
     * @var array<int, array{url: string, type: string, title?: string, subtitle?: string, link?: string}>
     */
    public array $slides = [];

    /**
     * Mount the component with slides data.
     *
     * @param  array<int, array{url: string, type: string, title?: string, subtitle?: string, link?: string}>  $slides
     */
    public function mount(array $slides = []): void
    {
        $this->slides = $slides;
    }

    public function render()
    {
        return view('livewire.media-slider');
    }
}
