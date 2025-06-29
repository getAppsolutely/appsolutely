<?php

declare(strict_types=1);

namespace App\Livewire;

final class TransitionSection extends BaseBlock
{
    protected array $defaultDisplayOptions = [
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

    protected function initializeComponent(): void
    {
        $transitionSection = $this->displayOptions;
        $default           = $this->defaultConfig();

        $this->displayOptions = [
            'image'               => $transitionSection['image'] ?? $default['image'],
            'height'              => $transitionSection['height'] ?? $default['height'],
            'background_position' => $transitionSection['background_position'] ?? $default['background_position'],
            'background_size'     => $transitionSection['background_size'] ?? $default['background_size'],
            'overlay'             => array_merge($default['overlay'], $transitionSection['overlay'] ?? []),
            'responsive'          => array_merge($default['responsive'], $transitionSection['responsive'] ?? []),
        ];
    }

    /**
     * Use it in blade file
     */
    public function getBackgroundStyle(): string
    {
        $styles = [];
        $config = $this->displayOptions;

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
}
