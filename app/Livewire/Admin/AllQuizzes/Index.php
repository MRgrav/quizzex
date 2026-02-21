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
    public $k = 1;

    public $search = '';
    public $status = '';
    public $type = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
    ];

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

    public function updated($property)
    {
        if (in_array($property, ['search', 'status', 'type'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $quizzes = Quiz::with('institute')
            ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->type, function ($q) {
                if ($this->type === 'school') {
                    $q->whereHas('institute', fn($iq) => $iq->where('type', 'school'));
                } elseif ($this->type === 'college') {
                    $q->whereHas('institute', fn($iq) => $iq->where('type', 'college'));
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.all-quizzes.index', [
            'quizzes' => $quizzes,
            'k' => $this->k
        ]);
    }
}
