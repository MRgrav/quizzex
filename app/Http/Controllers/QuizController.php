<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Institute;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Create a new quiz.
     * Admin can create CSIR quizzes (institute_id = null) or institute-specific quizzes.
     * Institute admins can only create quizzes for their own institute.
     */
    public function store(StoreQuizRequest $request): JsonResponse
    {
        // Authorize quiz creation using policy
        $this->authorize('create', Quiz::class);

        $user = $request->user();

        $data = $request->validated();
        $data['created_by'] = $user->id;

        // For institute admins, set institute_id to their institute
        if ($user->role === User::ROLE_INSTITUTE) {
            $institute = $user->institute;
            $data['institute_id'] = $institute->id;
        }

        $quiz = Quiz::create($data);

        return response()->json([
            'message' => 'Quiz created successfully.',
            'quiz' => $quiz->load(['institute', 'creator']),
        ], 201);
    }

    /**
     * Update a quiz.
     * Admin can update any quiz, institute can only update their own.
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz): JsonResponse
    {
        // Authorize quiz update using policy
        $this->authorize('update', $quiz);

        $data = $request->validated();

        $quiz->update($data);

        return response()->json([
            'message' => 'Quiz updated successfully.',
            'quiz' => $quiz->load(['institute', 'creator']),
        ]);
    }

    /**
     * List active quizzes visible to approved institutes and participants.
     * Shows:
     * - All active CSIR quizzes (institute_id = null)
     * - Active quizzes from approved institutes
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 15);

        $query = Quiz::where('status', Quiz::STATUS_ACTIVE)
            ->with(['institute', 'creator:id,name']);

        // Filter by institute if provided
        if ($request->has('institute_id')) {
            $request->validate([
                'institute_id' => ['nullable', 'exists:institutes,id'],
            ]);

            if ($request->institute_id === 'null' || $request->institute_id === null) {
                // Show only CSIR quizzes
                $query->whereNull('institute_id');
            } else {
                // Show quizzes from specific institute
                $query->where('institute_id', $request->institute_id);
            }
        }

        // If user is an institute admin, they can see:
        // - CSIR quizzes (institute_id = null)
        // - Quizzes from their own institute
        if ($user && $user->role === User::ROLE_INSTITUTE) {
            $institute = $user->institute;
            if ($institute && $institute->status === Institute::STATUS_APPROVED) {
                $query->where(function ($q) use ($institute) {
                    $q->whereNull('institute_id') // CSIR quizzes
                        ->orWhere('institute_id', $institute->id); // Their own quizzes
                });
            } else {
                // Institute not approved, can only see CSIR quizzes
                $query->whereNull('institute_id');
            }
        }

        // If user is a participant, they can see:
        // - CSIR quizzes
        // - Quizzes from their institute
        if ($user && $user->role === User::ROLE_PARTICIPANT) {
            $instituteId = $user->institute_id;
            if ($instituteId) {
                $query->where(function ($q) use ($instituteId) {
                    $q->whereNull('institute_id') // CSIR quizzes
                        ->orWhere('institute_id', $instituteId); // Their institute's quizzes
                });
            } else {
                // Participant without institute, only CSIR quizzes
                $query->whereNull('institute_id');
            }
        }

        // If user is admin, they see all active quizzes (no filter applied above)

        $quizzes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * Show a specific quiz with questions and options.
     * Visible to all approved institutes and participants.
     */
    public function show(Request $request, Quiz $quiz): JsonResponse
    {
        $user = $request->user();

        // Check if quiz is visible (must be active)
        if ($quiz->status !== Quiz::STATUS_ACTIVE) {
            return response()->json([
                'message' => 'Quiz is not available.',
            ], 404);
        }

        // Authorization checks
        if ($user) {
            // Institute admin can only see CSIR quizzes or their own institute's quizzes
            if ($user->role === User::ROLE_INSTITUTE) {
                $institute = $user->institute;
                if ($institute && $institute->status === Institute::STATUS_APPROVED) {
                    if ($quiz->institute_id !== null && $quiz->institute_id !== $institute->id) {
                        return response()->json([
                            'message' => 'Access denied.',
                        ], 403);
                    }
                } else {
                    // Institute not approved, can only see CSIR quizzes
                    if ($quiz->institute_id !== null) {
                        return response()->json([
                            'message' => 'Access denied.',
                        ], 403);
                    }
                }
            }

            // Participant can only see CSIR quizzes or their institute's quizzes
            if ($user->role === User::ROLE_PARTICIPANT) {
                $instituteId = $user->institute_id;
                if ($quiz->institute_id !== null && $quiz->institute_id !== $instituteId) {
                    return response()->json([
                        'message' => 'Access denied.',
                    ], 403);
                }
            }
        }

        // Load quiz with questions and options
        $quiz->load([
            'institute:id,name',
            'creator:id,name',
            'questions' => function ($query) {
                $query->orderBy('order');
            },
            'questions.options' => function ($query) {
                $query->orderBy('order');
            },
        ]);

        return response()->json([
            'quiz' => $quiz,
        ]);
    }
}
