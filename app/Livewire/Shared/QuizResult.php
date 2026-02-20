<?php

namespace App\Livewire\Shared;

use App\Models\QuizAttempt;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Quiz Result')]
class QuizResult extends Component
{
    public QuizAttempt $quizAttempt;
    public $quiz;
    public $answers;
    public $timeTaken;

    public $layout = 'layouts.organization';

    public function mount(QuizAttempt $quizAttempt)
    {
        // Verify this attempt belongs to the authenticated user
        if (auth()->user()->role != User::ROLE_ADMIN) {
            // dd($quizAttempt->quiz->organization_id == auth()->user()->organization_id);
            if ($quizAttempt->participant_id === auth()->id() || $quizAttempt->quiz->organization_id !== auth()->user()->organization_id) {
                abort(403, 'Unauthorized access to quiz result.');
            }
        }

        $this->quizAttempt = $quizAttempt;
        $this->quiz = $quizAttempt->quiz;

        // Load answers with questions and options
        $this->answers = $quizAttempt->answers()
            ->with(['question.options', 'option'])
            ->get();

        // Calculate time taken
        if ($quizAttempt->submitted_at && $quizAttempt->started_at) {
            $this->timeTaken = $quizAttempt->started_at->diffInMinutes($quizAttempt->submitted_at);
        }

        if (auth()->user()->role == User::ROLE_ADMIN) {
            $this->layout = 'layouts.admin';
        } else {
            $this->layout = 'layouts.participant';
        }
    }

    public function render()
    {
        return view('livewire.shared.quiz-result')->layout($this->layout);
    }
}
