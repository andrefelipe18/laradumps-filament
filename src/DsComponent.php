<?php

declare(strict_types = 1);

namespace LaraDumpsFilament;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DsComponent extends Component
{
    public function render(): View
    {
        return view('laradumps-filament::livewire.ds-load');
    }
}
