<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizAttemptPolicy
{
    /**
     * Determine whether the user can view the quiz attempt.
     * Users can view their own attempts, admins can view any attempt.
     */
    public function view(User $user, QuizAttempt $quizAttempt): bool
    {
        // Admins can view any attempt
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Users can view their own attempts
        return $quizAttempt->participant_id === $user->id;
    }

    /**
     * Determine whether the user can start a quiz attempt.
     * Only participants can start quiz attempts.
     */
    public function start(User $user, Quiz $quiz): bool
    {
        // Only participants can take quizzes
        if ($user->role !== User::ROLE_PARTICIPANT) {
            return false;
        }

        // Quiz must be active
        if ($quiz->status !== Quiz::STATUS_ACTIVE) {
            return false;
        }

        // Check if participant has access to this quiz
        $instituteId = $user->institute_id;

        // Can take CSIR quizzes (institute_id = null) or their institute's quizzes
        if ($quiz->institute_id !== null && $quiz->institute_id !== $instituteId) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can submit the quiz attempt.
     * Only the attempt owner can submit their own attempt.
     */
    public function submit(User $user, QuizAttempt $quizAttempt): bool
    {
        // Only the participant who started the attempt can submit it
        return $quizAttempt->participant_id === $user->id;
    }

    /**
     * Determine whether the user can view their own results.
     * Only participants can view their own results.
     */
    public function viewMyResults(User $user): bool
    {
        return $user->role === User::ROLE_PARTICIPANT;
    }

    /**
     * Determine whether the user can view institute results.
     * Only approved institute admins can view their institute's results.
     */
    public function viewInstituteResults(User $user): bool
    {
        // Must be an institute admin
        if ($user->role !== User::ROLE_INSTITUTE) {
            return false;
        }

        // Institute must exist and be approved
        $institute = $user->institute;
        return $institute && $institute->status === 'approved';
    }

    /**
     * Determine whether the user can view all results.
     * Only system admins can view all results.
     */
    public function viewAllResults(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
