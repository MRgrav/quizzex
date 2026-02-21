<?php

namespace App\Livewire\Shared;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Manage Questions')]
class ManageQuestions extends Component
{
    public Quiz $quiz;

    public $layout = 'layouts.organization';

    // New question form
    public $newQuestion = [
        'title' => '',
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

    public array $showAnswers = [];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz->load(['questions.options', 'institute']);

        // Initialize showAnswers array
        foreach ($this->quiz->questions as $question) {
            $this->showAnswers[$question->id] = false;
        }

        if (auth()->user()->role == \App\Models\User::ROLE_ADMIN) {
            $this->layout = 'layouts.admin';
        } else {
            $this->layout = 'layouts.organization';
        }
    }

    public function canEdit()
    {
        return $this->quiz->canBeEdited();
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
            'newQuestion.title' => 'required|string',
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
            'question_text' => $this->newQuestion['title'],
            'question_type' => $this->newQuestion['question_type'],
            'points' => $this->newQuestion['points'],
            'explanation' => $this->newQuestion['explanation'],
            'order' => $nextOrder,
        ]);

        // Create the options
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
            'title' => '',
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
        return view('livewire.shared.manage-questions')->layout($this->layout);
    }
}
