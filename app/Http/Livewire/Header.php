<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Component;

final class Header extends Component
{
    public function render(): object
    {
        return themed_view('livewire.header');
    }
}
