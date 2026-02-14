<?php

namespace App\Livewire\Participant;

use App\Models\Answer;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.quiz-attempt')]
#[Title('Take Quiz')]
class TakeQuiz extends Component
{
    public Quiz $quiz;
    public QuizAttempt $attempt;
    public array $answers = [];
    public int $currentQuestionIndex = 0;
    public int $remainingSeconds = 0;

    public function mount(Quiz $quiz)
    {
        $participant = auth()->user();
        $this->quiz = $quiz;

        // Check if quiz is live
        if (!$quiz->isLive()) {
            session()->flash('error', 'This quiz is not currently available.');
            return redirect()->route('participant.quizzes');
        }

        // Find or create attempt
        $this->attempt = QuizAttempt::firstOrCreate(
            [
                'participant_id' => $participant->id,
                'quiz_id' => $quiz->id,
                'status' => QuizAttempt::STATUS_IN_PROGRESS,
            ],
            [
                'started_at' => now(),
                'total_possible_score' => $quiz->total_points,
            ]
        );

        // Check if attempt is expired
        if ($this->attempt->isExpired()) {
            $this->autoSubmit();
            return;
        }

        // Load existing answers
        $existingAnswers = $this->attempt->answers()->get();
        foreach ($existingAnswers as $answer) {
            $this->answers[$answer->question_id] = $answer->option_id;
        }

        // Calculate remaining time
        $this->remainingSeconds = $this->attempt->getRemainingTime();
    }

    public function selectAnswer($questionId, $optionId)
    {
        $this->answers[$questionId] = $optionId;

        // Save answer to database
        Answer::updateOrCreate(
            [
                'quiz_attempt_id' => $this->attempt->id,
                'question_id' => $questionId,
            ],
            [
                'option_id' => $optionId,
            ]
        );
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < $this->quiz->questions()->count() - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        $this->currentQuestionIndex = $index;
    }

    public function submitQuiz()
    {
        // Calculate score
        $totalScore = 0;

        // Eager load questions and their correct options to avoid N+1
        $this->attempt->load(['answers.question.correctOptions']);

        foreach ($this->attempt->answers as $answer) {
            $question = $answer->question;
            $correctOption = $question->correctOptions->first();

            $isCorrect = $answer->option_id === $correctOption?->id;
            $pointsEarned = $isCorrect ? $question->points : 0;

            $answer->update([
                'is_correct' => $isCorrect,
                'points_earned' => $pointsEarned,
            ]);

            $totalScore += $pointsEarned;
        }

        // Update attempt
        $this->attempt->update([
            'score' => $totalScore,
            'submitted_at' => now(),
            'status' => QuizAttempt::STATUS_SUBMITTED,
        ]);

        // Redirect to results
        return redirect()->route('participant.quizzes.result', $this->attempt);
    }

    public function autoSubmit()
    {
        return $this->submitQuiz();
    }

    public function render()
    {
        $questions = $this->quiz->questions()->with('options')->get();
        $currentQuestion = $questions[$this->currentQuestionIndex] ?? null;

        return view('livewire.participant.take-quiz', [
            'currentQuestion' => $currentQuestion,
            'totalQuestions' => count($questions),
            'answeredCount' => count($this->answers),
        ]);
    }
}
