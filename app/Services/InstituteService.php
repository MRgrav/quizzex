<?php
// app/Services/InstituteService.php
namespace App\Services;

use App\Models\Institute;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class InstituteService
{
    public function createInstituteAdmin(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_INSTITUTE,
            'status' => User::STATUS_PENDING,
        ]);
    }

    public function createInstitute(User $user, array $data)
    {
        return Institute::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'address' => $data['address'],
            'contact_person' => $data['contact_person'],
            'phone' => $data['phone'],
            'user_id' => $user->id,
            'status' => Institute::STATUS_PENDING,
        ]);
    }

    public function getPaginated($filters = [], $perPage = 15)
    {
        $institutes = Institute::with('user:id,name,email')
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['type']), fn($q) => $q->where('type', $filters['type']))
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        return $institutes;
    }

    public function approveInstitute(Institute $institute)
    {
        $institute->update([
            'status' => Institute::STATUS_APPROVED,
        ]);

        $institute->user->update([
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function rejectInstitute(Institute $institute)
    {
        $institute->update([
            'status' => Institute::STATUS_REJECTED,
        ]);

        $institute->user->update([
            'status' => User::STATUS_BLOCKED,
        ]);
    }

    /**
     * Fetch participants specifically belonging to an institute.
     */
    public function getParticipants(Institute $institute, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $institute->participants() // Assumes hasMany relationship exists
            ->select('id', 'name', 'email', 'status', 'created_at')
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('email', 'like', '%' . $filters['search'] . '%');
                });
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
