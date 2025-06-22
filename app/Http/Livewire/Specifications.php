<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class Specifications extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $specifications = [];

    /**
     * Mount the component with specifications data.
     *
     * @param  array<string, mixed>  $specifications
     */
    public function mount(array $specifications = []): void
    {
        $this->specifications = array_merge([
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
        ], $specifications);
    }

    public function render(): object
    {
        return themed_view('livewire.specifications');
    }
}
