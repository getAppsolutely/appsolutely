<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class Specifications extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $config = [];

    /**
     * Mount the component with config data.
     *
     * @param  array<string, mixed>  $config
     */
    public function mount(array $config = []): void
    {
        $this->config = array_merge($this->defaultConfig(), $config);
    }

    /**
     * Get default specifications configuration.
     *
     * @return array<string, mixed>
     */
    private function defaultConfig(): array
    {
        return [
            'title'          => '',
            'subtitle'       => '',
            'description'    => '',
            'layout'         => 'grid', // grid, list, table
            'columns'        => 2, // Number of columns for grid layout
            'specifications' => [
                // Default structure for specifications
                // [
                //     'label' => 'Specification Name',
                //     'value' => 'Specification Value or Image Path',
                //     'type'  => 'text', // text, image, number, boolean
                //     'icon'  => '', // Optional icon class
                //     'unit'  => '', // Optional unit (e.g., 'kg', 'cm', '%')
                // ]
            ],
        ];
    }

    public function render(): object
    {
        return themed_view('livewire.specifications');
    }
}
