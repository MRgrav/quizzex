<?php

namespace App\Livewire\Admin\AllQuizzes;

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
    public array $showAnswers = [];

    // Quiz edit form
    public $title;
    public $description;
    public $duration_minutes;
    public $status;

    // New question form
    public $newQuestion = [
        'question_text' => '',
        'question_type' => 'multiple_choice',
        'points' => 1,
        'explanation' => '',
        'options' => [
            ['option_text' => '', 'is_correct' => false],
            ['option_text' => '', 'is_correct' => false],
            ['option_text' => '', 'is_correct' => false],
            ['option_text' => '', 'is_correct' => false],
        ]
    ];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load(['questions.options', 'institute', 'creator']);
        $this->title = $quiz->title;
        $this->description = $quiz->description;
        $this->duration_minutes = $quiz->duration_minutes;
        $this->status = $quiz->status;

        // Initialize showAnswers array
        foreach ($this->quiz->questions as $question) {
            $this->showAnswers[$question->id] = false;
        }
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

    public function toggleAnswers($questionId)
    {
        $this->showAnswers[$questionId] = !($this->showAnswers[$questionId] ?? false);
    }

    public function addOption()
    {
        $this->newQuestion['options'][] = [
            'option_text' => '',
            'is_correct' => false
        ];
    }

    public function removeOption($index)
    {
        if (count($this->newQuestion['options']) > 2) {
            unset($this->newQuestion['options'][$index]);
            $this->newQuestion['options'] = array_values($this->newQuestion['options']);
        }
    }

    public function addQuestion()
    {
        if (!$this->canEdit()) {
            session()->flash('error', 'Cannot add questions during active quiz period.');
            return;
        }

        // Base validation
        $this->validate([
            'newQuestion.question_text' => 'required|string',
            'newQuestion.question_type' => 'required|in:multiple_choice,true_false',
            'newQuestion.points' => 'required|integer|min:1',
            'newQuestion.explanation' => 'nullable|string',
        ]);

        // Conditional validation based on question type
        if ($this->newQuestion['question_type'] === 'multiple_choice') {
            $this->validate([
                'newQuestion.options.*.option_text' => 'required|string',
            ]);

            // Check if at least one option is marked as correct
            $hasCorrectAnswer = collect($this->newQuestion['options'])->contains('is_correct', true);

            if (!$hasCorrectAnswer) {
                session()->flash('error', 'Please mark at least one option as correct.');
                return;
            }
        } elseif ($this->newQuestion['question_type'] === 'true_false') {
            // For True/False, ensure we have exactly 2 options and one is correct
            if (count($this->newQuestion['options']) !== 2) {
                session()->flash('error', 'True/False questions must have exactly 2 options.');
                return;
            }

            $hasCorrectAnswer = collect($this->newQuestion['options'])->contains('is_correct', true);
            if (!$hasCorrectAnswer) {
                session()->flash('error', 'Please mark the correct answer.');
                return;
            }
        }

        // Get the next order number
        $nextOrder = $this->quiz->questions()->max('order') + 1;

        // Create the question
        $question = Question::create([
            'quiz_id' => $this->quiz->id,
            'question_text' => $this->newQuestion['question_text'],
            'question_type' => $this->newQuestion['question_type'],
            'points' => $this->newQuestion['points'],
            'explanation' => $this->newQuestion['explanation'],
            'order' => $nextOrder,
        ]);

        // Create the options (for both MCQ and True/False)
        foreach ($this->newQuestion['options'] as $index => $option) {
            if (!empty($option['option_text'])) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $option['option_text'],
                    'is_correct' => $option['is_correct'] ?? false,
                    'order' => $index,
                ]);
            }
        }

        // Update quiz totals
        $this->quiz->increment('total_questions');
        $this->quiz->increment('total_points', $this->newQuestion['points']);

        // Reset form
        $this->resetQuestionForm();

        // Reload quiz
        $this->quiz->load(['questions.options']);

        // Initialize showAnswers for new question
        $this->showAnswers[$question->id] = false;

        // Dispatch event to reset Trix editor
        $this->dispatch('question-added');

        session()->flash('success', 'Question added successfully!');
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);

        if ($question->quiz_id !== $this->quiz->id) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }

        $points = $question->points;
        $question->delete();

        // Update quiz totals
        $this->quiz->decrement('total_questions');
        $this->quiz->decrement('total_points', $points);

        // Reload quiz
        $this->quiz->load(['questions.options']);

        // Remove from showAnswers
        unset($this->showAnswers[$questionId]);

        session()->flash('success', 'Question deleted successfully!');
    }

    private function resetQuestionForm()
    {
        $this->newQuestion = [
            'question_text' => '',
            'question_type' => '',
            'points' => 1,
            'explanation' => '',
            'options' => [
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
            ]
        ];
    }

    public function updatedNewQuestionQuestionType($value)
    {
        // When question type changes, update options accordingly
        if ($value === 'true_false') {
            $this->newQuestion['options'] = [
                ['option_text' => 'True', 'is_correct' => false],
                ['option_text' => 'False', 'is_correct' => false],
            ];
        } elseif ($value === 'multiple_choice') {
            $this->newQuestion['options'] = [
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
            ];
        }
    }

    public function selectTrueFalseAnswer($index)
    {
        // For True/False questions, only one answer can be correct
        foreach ($this->newQuestion['options'] as $key => $option) {
            $this->newQuestion['options'][$key]['is_correct'] = ($key === $index);
        }
    }

    public function render()
    {
        return view('livewire.admin.all-quizzes.view-quiz');
    }
}
