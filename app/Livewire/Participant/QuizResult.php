<?php

namespace App\Livewire\Participant;

use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.participant')]
#[Title('Quiz Result')]
class QuizResult extends Component
{
    public QuizAttempt $quizAttempt;
    public $quiz;
    public $answers;
    public $timeTaken;

    public function mount(QuizAttempt $quizAttempt)
    {
        // Verify this attempt belongs to the authenticated user
        if ($quizAttempt->participant_id !== auth()->id()) {
            abort(403, 'Unauthorized access to quiz result.');
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
    }

    public function render()
    {
        return view('livewire.participant.quiz-result');
    }
}
