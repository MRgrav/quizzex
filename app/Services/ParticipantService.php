<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ParticipantService
{
    public $perPage = 10;
    public function addParticipant($data, $institute_id)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_PARTICIPANT,
            'status' => User::STATUS_ACTIVE, // Participants are active immediately
            'institute_id' => $institute_id, // Always set from authenticated admin's institute
        ]);
    }

    public function listInstitutesParticipant($institute)
    {
        return $institute->participants()
            ->select('id', 'name', 'email', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }
}