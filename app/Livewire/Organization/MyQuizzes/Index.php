<?php

namespace App\Livewire\Organization\MyQuizzes;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.organization')]
#[Title(('My Quizzes'))]
class Index extends Component
{
    public function render()
    {
        return view('livewire.organization.my-quizzes.index');
    }
}
