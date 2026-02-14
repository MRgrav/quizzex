<?php

namespace App\Livewire\Participant;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.participant')]
#[Title('My Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $participant = auth()->user();

        // Count available quizzes (live quizzes)
        $availableQuizzes = Quiz::where('status', Quiz::STATUS_ACTIVE)
            ->where(function ($query) use ($participant) {
                $query->whereNull('institute_id')
                    ->orWhere('institute_id', $participant->institute_id);
            })
            ->whereHas('questions') // Only quizzes with questions
            ->count();

        // Count completed attempts
        $completedAttempts = QuizAttempt::where('participant_id', $participant->id)
            ->where('status', QuizAttempt::STATUS_SUBMITTED)
            ->count();

        // Calculate average score
        $averageScore = QuizAttempt::where('participant_id', $participant->id)
            ->where('status', QuizAttempt::STATUS_SUBMITTED)
            ->avg('score');

        // Get recent attempts
        $recentAttempts = QuizAttempt::where('participant_id', $participant->id)
            ->where('status', QuizAttempt::STATUS_SUBMITTED)
            ->with('quiz')
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.participant.dashboard', [
            'availableQuizzes' => $availableQuizzes,
            'completedAttempts' => $completedAttempts,
            'averageScore' => $averageScore ? round($averageScore, 1) : 0,
            'recentAttempts' => $recentAttempts,
        ]);
    }
}
