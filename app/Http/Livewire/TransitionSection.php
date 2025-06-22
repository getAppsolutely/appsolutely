<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class TransitionSection extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $transitionSection = [];

    /**
     * Mount the component with transition section data.
     *
     * @param  array<string, mixed>  $transitionSection
     */
    public function mount(array $transitionSection = []): void
    {
        $default = $this->defaultConfig();

        // Manually merge nested arrays to ensure all keys exist
        $this->transitionSection = [
            'image'               => $transitionSection['image'] ?? $default['image'],
            'height'              => $transitionSection['height'] ?? $default['height'],
            'background_position' => $transitionSection['background_position'] ?? $default['background_position'],
            'background_size'     => $transitionSection['background_size'] ?? $default['background_size'],
            'overlay'             => array_merge($default['overlay'], $transitionSection['overlay'] ?? []),
            'responsive'          => array_merge($default['responsive'], $transitionSection['responsive'] ?? []),
        ];
    }

    /**
     * Get default transition section configuration.
     *
     * @return array<string, mixed>
     */
    private function defaultConfig(): array
    {
        return [
            'image'               => '', // Background image URL (required)
            'height'              => '300px', // Height in CSS units (e.g., '200px', '40vh')
            'background_position' => 'center center', // Background image position
            'background_size'     => 'cover', // Background image size (cover, contain, auto)
            'overlay'             => [
                'enabled' => false,
                'color'   => '#000000',
                'opacity' => 0.3,
            ],
            'responsive' => [
                'mobile_height'  => '200px',
                'hide_on_mobile' => false,
            ],
        ];
    }

    /**
     * Get computed background style.
     */
    public function getBackgroundStyle(): string
    {
        $styles = [];
        $config = $this->transitionSection;

        // Height
        $styles[] = 'height: ' . $config['height'];

        // Mobile height as CSS custom property
        $styles[] = '--mobile-height: ' . $config['responsive']['mobile_height'];

        // Background image
        if (! empty($config['image'])) {
            $styles[] = 'background-image: url(' . asset_server($config['image']) . ')';
            $styles[] = 'background-position: ' . $config['background_position'];
            $styles[] = 'background-size: ' . $config['background_size'];
            $styles[] = 'background-repeat: no-repeat';
        }

        return implode('; ', $styles);
    }

    public function render(): object
    {
        return themed_view('livewire.transition-section');
    }
}
