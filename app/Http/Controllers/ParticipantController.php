<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipantRequest;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParticipantController extends Controller
{
    /**
     * Create a new participant account (institute admin only).
     * The participant will automatically belong to the authenticated institute admin's institute.
     */
    public function store(StoreParticipantRequest $request): JsonResponse
    {
        $instituteAdmin = $request->user();
        
        // Use policy to authorize
        $this->authorize('create', User::class);

        $institute = $instituteAdmin->institute;

        // Create participant account - institute_id is set from authenticated admin's institute
        $participant = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_PARTICIPANT,
            'status' => User::STATUS_ACTIVE, // Participants are active immediately
            'institute_id' => $institute->id, // Always set from authenticated admin's institute
        ]);

        return response()->json([
            'message' => 'Participant created successfully.',
            'participant' => [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'role' => $participant->role,
                'status' => $participant->status,
                'institute_id' => $participant->institute_id,
                'created_at' => $participant->created_at,
            ],
        ], 201);
    }

    /**
     * List all participants for the authenticated institute admin.
     */
    public function index(Request $request): JsonResponse
    {
        $instituteAdmin = $request->user();
        
        // Use policy to authorize
        $this->authorize('viewAny', User::class);

        $institute = $instituteAdmin->institute;
        $perPage = $request->get('per_page', 15);

        // Only get participants from this institute admin's institute
        $participants = $institute->participants()
            ->select('id', 'name', 'email', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'institute' => [
                'id' => $institute->id,
                'name' => $institute->name,
            ],
            'participants' => $participants,
        ]);
    }

    /**
     * Show a specific participant (institute admin can only view their own participants).
     */
    public function show(Request $request, User $participant): JsonResponse
    {
        // Use policy to authorize - ensures participant belongs to admin's institute
        $this->authorize('view', $participant);

        return response()->json([
            'participant' => [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'status' => $participant->status,
                'institute_id' => $participant->institute_id,
                'created_at' => $participant->created_at,
                'updated_at' => $participant->updated_at,
            ],
        ]);
    }
}
