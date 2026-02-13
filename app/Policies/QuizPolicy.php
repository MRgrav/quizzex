<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizPolicy
{
    /**
     * Determine whether the user can view any quizzes.
     * All authenticated users can view quizzes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the quiz.
     * Authorization is handled in the controller based on quiz visibility rules.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // All authenticated users can view active quizzes
        // Specific access rules (CSIR vs institute quizzes) are handled in controller
        return true;
    }

    /**
     * Determine whether the user can create quizzes.
     * Only admins and approved institute admins can create quizzes.
     */
    public function create(User $user): bool
    {
        // Admins can always create quizzes
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Institute admins can create quizzes if their institute is approved
        if ($user->role === User::ROLE_INSTITUTE) {
            $institute = $user->institute;
            return $institute && $institute->status === 'approved';
        }

        // Participants cannot create quizzes
        return false;
    }

    /**
     * Determine whether the user can update the quiz.
     * Admins can update any quiz.
     * Institute admins can only update quizzes from their own institute.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        // Admins can update any quiz
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Institute admins can only update their own institute's quizzes
        if ($user->role === User::ROLE_INSTITUTE) {
            $institute = $user->institute;

            // Institute must be approved
            if (!$institute || $institute->status !== 'approved') {
                return false;
            }

            // Quiz must belong to this institute (not CSIR quizzes)
            return $quiz->institute_id === $institute->id;
        }

        // Participants cannot update quizzes
        return false;
    }

    /**
     * Determine whether the user can delete the quiz.
     * Same rules as update.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        return $this->update($user, $quiz);
    }

    /**
     * Determine whether the user can restore the quiz.
     */
    public function restore(User $user, Quiz $quiz): bool
    {
        return $this->update($user, $quiz);
    }

    /**
     * Determine whether the user can permanently delete the quiz.
     */
    public function forceDelete(User $user, Quiz $quiz): bool
    {
        return $this->delete($user, $quiz);
    }
}
