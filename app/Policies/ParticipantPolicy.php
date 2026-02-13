<?php

namespace App\Policies;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ParticipantPolicy
{
    /**
     * Determine whether the user can view any participants.
     * Admins can view all participants, institute admins can view their own.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all participants
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Only institute admins can view participants
        if ($user->role !== User::ROLE_INSTITUTE) {
            return false;
        }

        // Institute must be approved
        $institute = $user->institute;
        return $institute && $institute->status === Institute::STATUS_APPROVED;
    }

    /**
     * Determine whether the user can view a specific participant.
     * Admins can view any participant, institute admins can view their own.
     */
    public function view(User $user, User $participant): bool
    {
        // Admins can view any participant
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // User must be an institute admin
        if ($user->role !== User::ROLE_INSTITUTE) {
            return false;
        }

        // Participant must actually be a participant
        if ($participant->role !== User::ROLE_PARTICIPANT) {
            return false;
        }

        // Get the institute admin's institute
        $institute = $user->institute;
        if (!$institute || $institute->status !== Institute::STATUS_APPROVED) {
            return false;
        }

        // Participant must belong to the institute admin's institute
        return $participant->institute_id === $institute->id;
    }

    /**
     * Determine whether the user can create participants.
     * Admins can create participants for any institute.
     * Institute admins can create participants for their own institute.
     */
    public function create(User $user): bool
    {
        // Admins can create participants for any institute
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // User must be an institute admin
        if ($user->role !== User::ROLE_INSTITUTE) {
            return false;
        }

        // Institute must exist and be approved
        $institute = $user->institute;
        if (!$institute || $institute->status !== Institute::STATUS_APPROVED) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update a participant.
     * Participants can only be updated by their own institute admin.
     */
    public function update(User $user, User $participant): bool
    {
        // User must be an institute admin
        if ($user->role !== User::ROLE_INSTITUTE) {
            return false;
        }

        // Participant must actually be a participant
        if ($participant->role !== User::ROLE_PARTICIPANT) {
            return false;
        }

        // Get the institute admin's institute
        $institute = $user->institute;
        if (!$institute || $institute->status !== Institute::STATUS_APPROVED) {
            return false;
        }

        // Participant must belong to the institute admin's institute
        return $participant->institute_id === $institute->id;
    }

    /**
     * Determine whether the user can delete a participant.
     * Participants can only be deleted by their own institute admin.
     */
    public function delete(User $user, User $participant): bool
    {
        // User must be an institute admin
        if ($user->role !== User::ROLE_INSTITUTE) {
            return false;
        }

        // Participant must actually be a participant
        if ($participant->role !== User::ROLE_PARTICIPANT) {
            return false;
        }

        // Get the institute admin's institute
        $institute = $user->institute;
        if (!$institute || $institute->status !== Institute::STATUS_APPROVED) {
            return false;
        }

        // Participant must belong to the institute admin's institute
        return $participant->institute_id === $institute->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $participant): bool
    {
        return $this->update($user, $participant);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $participant): bool
    {
        return $this->delete($user, $participant);
    }
}
