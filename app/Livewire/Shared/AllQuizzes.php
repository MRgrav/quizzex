<?php

namespace App\Livewire\Shared;

use App\Models\Quiz;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;


#[Title('All Quizzes')]
class AllQuizzes extends Component
{
    use WithPagination;
    public string $layout = 'layouts.admin';
    public string $redirectRoute = 'admin.all-quizzes';
    public $quizzes;
    public $k = 1;

    #[Computed()]
    public function mount(Quiz $quiz)
    {
        // Detect user role and set appropriate layout and redirect
        $user = auth()->user();

        if ($user->role === User::ROLE_INSTITUTE) {
            $this->layout = 'layouts.organization';
            $this->redirectRoute = 'organization.my-quizzes';
        }

        if ($user->role === User::ROLE_INSTITUTE) {
            $this->quizzes = $quiz->where('institute_id', $user->institute_id)->get();
        } else {
            $this->quizzes = $quiz->get();
        }
    }

    public function render()
    {
        return view('livewire.shared.all-quizzes', [
            'quizzes' => $this->quizzes,
            'k' => $this->k
        ])->layout($this->layout);
    }
}
