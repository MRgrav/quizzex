<?php

namespace App\Livewire\Organization\MyQuizzes;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.organization')]
#[Title(('My Quizzes'))]
class Index extends Component
{
    #[Computed(persist: true, seconds: 1200)]
    public function render()
    {
        return view('livewire.organization.my-quizzes.index');
    }
}
