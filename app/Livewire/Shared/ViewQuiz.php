<?php

namespace App\Livewire\Shared;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('View Quiz')]
class ViewQuiz extends Component
{
    public Quiz $quiz;
    public bool $editMode = false;

    // Quiz edit form
    public $title;
    public $description;
    public $duration_minutes;
    public $status;

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load(['questions.options', 'institute', 'creator']);
        $this->title = $quiz->title;
        $this->description = $quiz->description;
        $this->duration_minutes = $quiz->duration_minutes;
        $this->status = $quiz->status;
    }

    public function canEdit()
    {
        return $this->quiz->canBeEdited();
    }

    public function toggleEdit()
    {
        if (!$this->canEdit()) {
            session()->flash('error', 'Cannot edit quiz during active period.');
            return;
        }

        $this->editMode = !$this->editMode;

        if (!$this->editMode) {
            // Reset to original values if canceling
            $this->title = $this->quiz->title;
            $this->description = $this->quiz->description;
            $this->duration_minutes = $this->quiz->duration_minutes;
            $this->status = $this->quiz->status;
        }
    }

    public function saveQuiz()
    {
        if (!$this->canEdit()) {
            session()->flash('error', 'Cannot edit quiz during active period.');
            return;
        }

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,inactive',
        ]);

        $this->quiz->update([
            'title' => $this->title,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'status' => $this->status,
        ]);

        $this->editMode = false;
        session()->flash('success', 'Quiz updated successfully!');
    }



    public function render()
    {
        return view('livewire.shared.view-quiz');
    }
}
