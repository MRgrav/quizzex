<?php

namespace App\Livewire\Participant\AvailableQuizzes;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.participant')]
#[Title('Available Quizzes')]
class Index extends Component
{
    use WithPagination;
    public function startQuiz(Quiz $quiz)
    {
        $participant = auth()->user();

        // Check if participant already has an in-progress attempt
        $existingAttempt = QuizAttempt::where('participant_id', $participant->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
            ->first();

        if ($existingAttempt) {
            // Resume existing attempt
            return redirect()->route('participant.quizzes.attempt', $quiz);
        }

        // Check if quiz is live
        if (!$quiz->isLive()) {
            session()->flash('error', 'This quiz is not currently available.');
            return;
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'participant_id' => $participant->id,
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'status' => QuizAttempt::STATUS_IN_PROGRESS,
            'total_possible_score' => $quiz->total_points,
        ]);

        return redirect()->route('participant.quizzes.attempt', $quiz);
    }

    public function render()
    {
        $participant = auth()->user();

        // Get all active quizzes visible to this participant's institute
        $quizzes = Quiz::where('status', Quiz::STATUS_ACTIVE)
            ->where(function ($query) use ($participant) {
                // CSIR quizzes (no institute_id) OR quizzes from participant's institute
                $query->whereNull('institute_id')
                    ->orWhere('institute_id', $participant->institute_id);
            })
            ->with([
                'attempts' => function ($query) use ($participant) {
                    $query->where('participant_id', $participant->id);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('livewire.participant.available-quizzes.index', [
            'quizzes' => $quizzes,
        ]);
    }
}
