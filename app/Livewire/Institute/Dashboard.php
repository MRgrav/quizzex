<?php

namespace App\Livewire\Institute;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.institute')]
#[Title('Institute Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.institute.dashboard');
    }
}
