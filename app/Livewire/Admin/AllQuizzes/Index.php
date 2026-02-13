<?php

namespace App\Livewire\Admin\AllQuizzes;

use App\Models\Quiz;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('All Quizzes')]
class Index extends Component
{
    use WithPagination;
    public $quizzes;
    public $k = 1;

    public function mount(Quiz $quiz)
    {
        $this->quizzes = $quiz->all();
    }

    public function render()
    {
        return view('livewire.admin.all-quizzes.index', [
            'quizzes' => $this->quizzes,
            'k' => $this->k
        ]);
    }
}
