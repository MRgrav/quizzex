<?php

namespace App\Livewire\Organization\Results;

use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.organization')]
#[Title('Results')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'submitted_at';
    public string $sortDir = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function render()
    {
        $instituteId = auth()->user()->institute_id;
        $search = $this->search;
        $sortBy = $this->sortBy;
        $sortDir = $this->sortDir;

        $query = QuizAttempt::where('status', QuizAttempt::STATUS_SUBMITTED)
            ->with(['quiz', 'participant'])
            ->whereHas('quiz', fn($q) => $q->where('institute_id', $instituteId))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->whereHas('participant', fn($u) => $u->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('quiz', fn($quiz) => $quiz->where('title', 'like', '%' . $search . '%'));
                });
            });

        if ($sortBy === 'percentage') {
            $query->orderByRaw('(score / NULLIF(total_possible_score, 0)) ' . $sortDir);
        } elseif ($sortBy === 'participant_name') {
            $query->join('users', 'quiz_attempts.participant_id', '=', 'users.id')
                ->orderBy('users.name', $sortDir)
                ->select('quiz_attempts.*');
        } elseif ($sortBy === 'quiz_title') {
            $query->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                ->orderBy('quizzes.title', $sortDir)
                ->select('quiz_attempts.*');
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        return view('livewire.organization.results.index', [
            'attempts' => $query->paginate(15),
        ]);
    }
}
