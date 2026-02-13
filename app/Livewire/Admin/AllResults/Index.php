<?php

namespace App\Livewire\Admin\AllResults;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('All Results')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.all-results.index');
    }
}
