<?php

namespace App\Livewire\Participant;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.participant')]
#[Title('My Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.participant.dashboard');
    }
}
