<?php

namespace LaradumpsFilament;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DsComponent extends Component
{
    public function render(): View
    {
        return view('laradumps-filament::livewire.ds-load');
    }
}
