<?php

namespace App\Livewire\Participant\MyResults;

use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.participant')]
#[Title('My Results')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $attempts = QuizAttempt::where('participant_id', auth()->id())
            ->where('status', QuizAttempt::STATUS_SUBMITTED)
            ->with('quiz')
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('livewire.participant.my-results.index', [
            'attempts' => $attempts,
        ]);
    }
}
