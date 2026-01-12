<?php

declare(strict_types=1);

namespace App\Livewire;

final class StoreLocations extends GeneralBlock
{
    protected array $defaultDisplayOptions = [
        'title'       => 'Store Locations',
        'subtitle'    => 'Find a Location Near You',
        'description' => 'Visit our retail points for personalized services and unique in-store experiences.',
        'layout'      => 'grid',
        'columns'     => 2,
        'show_map'    => true,
        'map_api_key' => 'YOUR_API_KEY_PLACEHOLDER',
        'locations'   => [
            [
                'name'      => 'Location A',
                'address'   => '123 Main Street',
                'city'      => 'City Name',
                'state'     => 'Region',
                'zip_code'  => '0000',
                'country'   => 'Country',
                'phone'     => '+00 0 0000 0000',
                'email'     => 'contact@store.example',
                'website'   => 'https://example.com/location-a',
                'hours'     => 'Mon-Fri: 9AM-6PM, Sat: 9AM-5PM, Sun: 10AM-4PM',
                'image'     => '/images/locations/location-a.jpg',
                'latitude'  => '-00.0000',
                'longitude' => '000.0000',
                'services'  => [
                    'Personal Assistance',
                    'Pickup Service',
                    'Gift Services',
                    'Adjustments',
                ],
                'manager'     => 'Jane Doe',
                'established' => '2018',
                'type'        => 'Flagship Store',
                'featured'    => true,
            ],
        ],
    ];
}
