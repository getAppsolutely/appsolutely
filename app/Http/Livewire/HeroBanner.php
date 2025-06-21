<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class HeroBanner extends Component
{
    /**
     * @var array<int, array{url: string, type: string, title?: string, subtitle?: string, link?: string}>
     */
    public array $heros = [];

    /**
     * Mount the component with heros data.
     *
     * @param  array<int, array{url: string, type: string, title?: string, subtitle?: string, link?: string}>  $heros
     */
    public function mount(array $heros = []): void
    {
        $this->heros = $heros;
    }

    public function render()
    {
        return view('livewire.hero-banner');
    }
}
