<?php

namespace App\Livewire\Admin\AllResults;

use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('All Results')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $attempts = QuizAttempt::where('status', QuizAttempt::STATUS_SUBMITTED)
            ->with(['quiz', 'participant'])
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.all-results.index', [
            'attempts' => $attempts,
        ]);
    }
}
