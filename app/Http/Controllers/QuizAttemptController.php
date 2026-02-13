<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartQuizAttemptRequest;
use App\Http\Requests\SubmitQuizAnswersRequest;
use App\Models\Answer;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{
    /**
     * Start a new quiz attempt.
     * Prevents duplicate attempts - users can only have one active or completed attempt per quiz.
     */
    public function start(StartQuizAttemptRequest $request): JsonResponse
    {
        $user = $request->user();
        $quizId = $request->validated()['quiz_id'];

        $quiz = Quiz::with(['questions.options'])->findOrFail($quizId);

        // Authorize using policy
        $this->authorize('start', [QuizAttempt::class, $quiz]);

        // Check for existing attempts (prevent duplicates)
        $existingAttempt = QuizAttempt::where('participant_id', $user->id)
            ->where('quiz_id', $quizId)
            ->whereIn('status', [QuizAttempt::STATUS_IN_PROGRESS, QuizAttempt::STATUS_SUBMITTED])
            ->first();

        if ($existingAttempt) {
            return response()->json([
                'message' => 'You already have an active or completed attempt for this quiz.',
                'existing_attempt' => $existingAttempt,
            ], 409);
        }

        // Calculate total possible score from questions
        $totalPossibleScore = $quiz->questions->sum('points');

        // Create new attempt
        $attempt = QuizAttempt::create([
            'participant_id' => $user->id,
            'quiz_id' => $quizId,
            'started_at' => now(),
            'status' => QuizAttempt::STATUS_IN_PROGRESS,
            'total_possible_score' => $totalPossibleScore,
        ]);

        // Load attempt with quiz and questions
        $attempt->load(['quiz', 'quiz.questions.options']);

        return response()->json([
            'message' => 'Quiz attempt started successfully.',
            'attempt' => $attempt,
            'duration_minutes' => $quiz->duration_minutes,
            'expires_at' => $attempt->started_at->addMinutes($quiz->duration_minutes),
        ], 201);
    }

    /**
     * Submit quiz answers and calculate score.
     * Validates time duration and auto-grades MCQ/True-False questions.
     */
    public function submit(SubmitQuizAnswersRequest $request, QuizAttempt $quizAttempt): JsonResponse
    {
        // Authorize using policy
        $this->authorize('submit', $quizAttempt);

        $user = $request->user();

        // Check if already submitted
        if ($quizAttempt->status === QuizAttempt::STATUS_SUBMITTED) {
            return response()->json([
                'message' => 'This attempt has already been submitted.',
            ], 400);
        }

        // Check if attempt is in progress
        if ($quizAttempt->status !== QuizAttempt::STATUS_IN_PROGRESS) {
            return response()->json([
                'message' => 'This attempt cannot be submitted.',
            ], 400);
        }

        // Load quiz with duration
        $quiz = $quizAttempt->quiz()->with(['questions.options'])->first();

        // Check time duration - enforce quiz time limit
        if ($quiz->duration_minutes) {
            $expiresAt = $quizAttempt->started_at->addMinutes($quiz->duration_minutes);
            if (now()->isAfter($expiresAt)) {
                // Mark as abandoned if submitted after time limit
                $quizAttempt->update([
                    'status' => QuizAttempt::STATUS_ABANDONED,
                ]);

                return response()->json([
                    'message' => 'Quiz time limit exceeded. Your attempt has been marked as abandoned.',
                    'expired_at' => $expiresAt,
                ], 400);
            }
        }

        $answers = $request->validated()['answers'];
        $totalScore = 0;

        DB::beginTransaction();
        try {
            // Process each answer
            foreach ($answers as $answerData) {
                $questionId = $answerData['question_id'];
                $optionId = $answerData['option_id'] ?? null;
                $answerText = $answerData['answer_text'] ?? null;

                // Get the question
                $question = $quiz->questions->firstWhere('id', $questionId);

                if (!$question) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Question ID {$questionId} does not belong to this quiz.",
                    ], 400);
                }

                $isCorrect = false;
                $pointsEarned = 0;

                // Auto-grade multiple choice and true/false questions
                if (in_array($question->question_type, ['multiple_choice', 'true_false'])) {
                    if ($optionId) {
                        $selectedOption = $question->options->firstWhere('id', $optionId);

                        if ($selectedOption && $selectedOption->is_correct) {
                            $isCorrect = true;
                            $pointsEarned = $question->points;
                            $totalScore += $pointsEarned;
                        }
                    }
                }
                // Short answer questions default to 0 (manual grading required)
                // Future enhancement: implement manual grading interface

                // Save the answer
                Answer::create([
                    'quiz_attempt_id' => $quizAttempt->id,
                    'question_id' => $questionId,
                    'option_id' => $optionId,
                    'answer_text' => $answerText,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                ]);
            }

            // Update attempt with final score and submission time
            $quizAttempt->update([
                'score' => $totalScore,
                'submitted_at' => now(),
                'status' => QuizAttempt::STATUS_SUBMITTED,
            ]);

            DB::commit();

            // Load attempt with answers and correct options for review
            $quizAttempt->load([
                'answers.question.options',
                'answers.option',
                'quiz',
            ]);

            return response()->json([
                'message' => 'Quiz submitted successfully.',
                'attempt' => $quizAttempt,
                'score' => $totalScore,
                'total_possible_score' => $quizAttempt->total_possible_score,
                'percentage' => $quizAttempt->percentage,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while submitting your quiz.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific quiz attempt with results.
     * Only accessible to the attempt owner or admin.
     */
    public function show(Request $request, QuizAttempt $quizAttempt): JsonResponse
    {
        $user = $request->user();

        // Check authorization
        if ($user->role !== User::ROLE_ADMIN && $quizAttempt->participant_id !== $user->id) {
            return response()->json([
                'message' => 'You do not have permission to view this attempt.',
            ], 403);
        }

        // Load attempt with all related data
        $quizAttempt->load([
            'quiz',
            'participant:id,name,email',
            'answers.question.options',
            'answers.option',
        ]);

        return response()->json([
            'attempt' => $quizAttempt,
        ]);
    }

    /**
     * List all quiz attempts for the authenticated user.
     * Admins can see all attempts, participants see only their own.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);

        $query = QuizAttempt::with(['quiz:id,title,duration_minutes', 'participant:id,name,email']);

        // Filter by user role
        if ($user->role === User::ROLE_PARTICIPANT) {
            $query->where('participant_id', $user->id);
        }

        // Optional filters
        if ($request->has('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->has('status')) {
            $request->validate([
                'status' => ['in:in_progress,submitted,abandoned'],
            ]);
            $query->where('status', $request->status);
        }

        $attempts = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'attempts' => $attempts,
        ]);
    }

    /**
     * Get participant's own quiz results.
     * Participants can only view their own attempts.
     */
    public function myResults(Request $request): JsonResponse
    {
        // Authorize using policy
        $this->authorize('viewMyResults', QuizAttempt::class);

        $user = $request->user();
        $perPage = $request->get('per_page', 15);

        $query = QuizAttempt::with([
            'quiz:id,title,duration_minutes,total_points',
            'answers.question:id,question_text,points',
            'answers.option:id,option_text,is_correct',
        ])->where('participant_id', $user->id);

        // Optional filters
        if ($request->has('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->has('status')) {
            $request->validate([
                'status' => ['in:in_progress,submitted,abandoned'],
            ]);
            $query->where('status', $request->status);
        }

        // Only show submitted attempts by default (can be overridden with status filter)
        if (!$request->has('status')) {
            $query->where('status', QuizAttempt::STATUS_SUBMITTED);
        }

        $results = $query->orderBy('submitted_at', 'desc')->paginate($perPage);

        return response()->json([
            'results' => $results,
        ]);
    }

    /**
     * Get quiz results for all participants in the institute.
     * Institute admins can only view results from their own institute.
     */
    public function instituteResults(Request $request): JsonResponse
    {
        // Authorize using policy
        $this->authorize('viewInstituteResults', QuizAttempt::class);

        $user = $request->user();
        $institute = $user->institute;

        if (!$institute) {
            return response()->json([
                'message' => 'No institute associated with your account.',
            ], 403);
        }

        $perPage = $request->get('per_page', 15);

        // Get all participants from this institute
        $query = QuizAttempt::with([
            'quiz:id,title,duration_minutes,total_points,institute_id',
            'participant:id,name,email,institute_id',
            'answers.question:id,question_text,points',
        ])->whereHas('participant', function ($q) use ($institute) {
            $q->where('institute_id', $institute->id);
        });

        // Optional filters
        if ($request->has('participant_id')) {
            $request->validate([
                'participant_id' => ['exists:users,id'],
            ]);

            // Ensure participant belongs to this institute
            $query->whereHas('participant', function ($q) use ($request, $institute) {
                $q->where('id', $request->participant_id)
                    ->where('institute_id', $institute->id);
            });
        }

        if ($request->has('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->has('status')) {
            $request->validate([
                'status' => ['in:in_progress,submitted,abandoned'],
            ]);
            $query->where('status', $request->status);
        }

        // Only show submitted attempts by default
        if (!$request->has('status')) {
            $query->where('status', QuizAttempt::STATUS_SUBMITTED);
        }

        $results = $query->orderBy('submitted_at', 'desc')->paginate($perPage);

        return response()->json([
            'results' => $results,
            'institute' => [
                'id' => $institute->id,
                'name' => $institute->name,
            ],
        ]);
    }

    /**
     * Get all quiz results across all institutes.
     * System admins only - full access to all attempts.
     */
    public function allResults(Request $request): JsonResponse
    {
        // Authorize using policy
        $this->authorize('viewAllResults', QuizAttempt::class);

        $perPage = $request->get('per_page', 15);

        $query = QuizAttempt::with([
            'quiz:id,title,duration_minutes,total_points,institute_id',
            'participant:id,name,email,institute_id',
            'participant.participantInstitute:id,name',
            'answers.question:id,question_text,points',
        ]);

        // Optional filters
        if ($request->has('institute_id')) {
            $request->validate([
                'institute_id' => ['nullable', 'exists:institutes,id'],
            ]);

            if ($request->institute_id === 'null' || $request->institute_id === null) {
                // Show participants with no institute
                $query->whereHas('participant', function ($q) {
                    $q->whereNull('institute_id');
                });
            } else {
                $query->whereHas('participant', function ($q) use ($request) {
                    $q->where('institute_id', $request->institute_id);
                });
            }
        }

        if ($request->has('participant_id')) {
            $request->validate([
                'participant_id' => ['exists:users,id'],
            ]);
            $query->where('participant_id', $request->participant_id);
        }

        if ($request->has('quiz_id')) {
            $query->where('quiz_id', $request->quiz_id);
        }

        if ($request->has('status')) {
            $request->validate([
                'status' => ['in:in_progress,submitted,abandoned'],
            ]);
            $query->where('status', $request->status);
        }

        // Only show submitted attempts by default
        if (!$request->has('status')) {
            $query->where('status', QuizAttempt::STATUS_SUBMITTED);
        }

        $results = $query->orderBy('submitted_at', 'desc')->paginate($perPage);

        return response()->json([
            'results' => $results,
        ]);
    }
}
