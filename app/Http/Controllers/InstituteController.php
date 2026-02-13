<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstituteRequest;
use App\Models\Institute;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class InstituteController extends Controller
{
    /**
     * Register a new institute (public endpoint).
     * Creates both the user account and institute record.
     */
    public function register(StoreInstituteRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create user account with institute role
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_INSTITUTE,
                'status' => User::STATUS_PENDING,
            ]);

            // Create institute record
            $institute = Institute::create([
                'name' => $request->institute_name,
                'type' => $request->type,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'user_id' => $user->id,
                'status' => Institute::STATUS_PENDING,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Institute registration successful. Please wait for admin approval.',
                'institute' => $institute->load('user'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Approve an institute (admin only).
     */
    public function approve(Institute $institute): JsonResponse
    {
        if ($institute->status !== Institute::STATUS_PENDING) {
            return response()->json([
                'message' => 'Only pending institutes can be approved.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $institute->update([
                'status' => Institute::STATUS_APPROVED,
            ]);

            // Activate the user account
            $institute->user->update([
                'status' => User::STATUS_ACTIVE,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Institute approved successfully.',
                'institute' => $institute->load('user'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Approval failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Reject an institute (admin only).
     */
    public function reject(Institute $institute): JsonResponse
    {
        if ($institute->status !== Institute::STATUS_PENDING) {
            return response()->json([
                'message' => 'Only pending institutes can be rejected.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $institute->update([
                'status' => Institute::STATUS_REJECTED,
            ]);

            // Block the user account
            $institute->user->update([
                'status' => User::STATUS_BLOCKED,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Institute rejected successfully.',
                'institute' => $institute->load('user'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Rejection failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * List all pending institutes (admin only).
     */
    public function pending(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);

        $institutes = Institute::where('status', Institute::STATUS_PENDING)
            ->with('user:id,name,email,created_at')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'institutes' => $institutes,
        ]);
    }

    /**
     * List all institutes with filters (admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Institute::with('user:id,name,email,created_at');

        // Filter by status
        if ($request->has('status')) {
            $request->validate([
                'status' => ['required', Rule::in(Institute::STATUSES)],
            ]);
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $request->validate([
                'type' => ['required', Rule::in(Institute::TYPES)],
            ]);
            $query->where('type', $request->type);
        }

        $perPage = $request->get('per_page', 15);
        $institutes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'institutes' => $institutes,
        ]);
    }
}
