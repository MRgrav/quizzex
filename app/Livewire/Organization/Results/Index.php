<?php

namespace App\Livewire\Organization\Results;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.organization')]
#[Title('Results')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.organization.results.index');
    }
}
