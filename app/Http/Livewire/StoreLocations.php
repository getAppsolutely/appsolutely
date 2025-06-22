<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class StoreLocations extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $storeLocations = [];

    /**
     * Mount the component with store locations data.
     *
     * @param  array<string, mixed>  $storeLocations
     */
    public function mount(array $storeLocations = []): void
    {
        $this->storeLocations = array_merge([
            'title'       => '',
            'subtitle'    => '',
            'description' => '',
            'layout'      => 'grid', // grid, list, table, map
            'columns'     => 3, // Number of columns for grid layout
            'show_map'    => false, // Whether to show map integration
            'map_api_key' => '', // Google Maps API key
            'locations'   => [
                // Default structure for store locations
                // [
                //     'name'        => 'Store Name',
                //     'address'     => 'Full Address',
                //     'city'        => 'City',
                //     'state'       => 'State/Province',
                //     'zip_code'    => 'Postal Code',
                //     'country'     => 'Country',
                //     'phone'       => 'Phone Number',
                //     'email'       => 'Email Address',
                //     'website'     => 'Website URL',
                //     'hours'       => 'Operating Hours',
                //     'image'       => 'Store Image URL',
                //     'latitude'    => 'GPS Latitude',
                //     'longitude'   => 'GPS Longitude',
                //     'services'    => ['Service 1', 'Service 2'], // Available services
                //     'manager'     => 'Store Manager Name',
                //     'established' => 'Year Established',
                //     'type'        => 'Store Type (flagship, outlet, etc.)',
                //     'featured'    => false, // Whether this is a featured location
                // ]
            ],
        ], $storeLocations);
    }

    public function render(): object
    {
        return themed_view('livewire.store-locations');
    }
}
