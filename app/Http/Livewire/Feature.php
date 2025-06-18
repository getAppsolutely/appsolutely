<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class Feature extends Component
{
    public function render(): object
    {
        return themed_view('livewire.feature');
    }
}
