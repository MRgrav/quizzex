<?php

namespace App\Http\Requests;

use App\Models\Institute;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only admin or approved institute admins can create quizzes.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        
        if (!$user) {
            return false;
        }

        // Admin can create any quiz (CSIR or institute-specific)
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Institute admin can only create quizzes for their own institute
        if ($user->role === User::ROLE_INSTITUTE) {
            $institute = $user->institute;
            // Institute must be approved
            if (!$institute || $institute->status !== Institute::STATUS_APPROVED) {
                return false;
            }
            // If institute_id is provided, it must match the user's institute
            if ($this->has('institute_id') && $this->institute_id !== $institute->id) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'string', Rule::in(Quiz::STATUSES)],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ];

        // Admin can set institute_id or leave it null (CSIR quiz)
        if ($user->role === User::ROLE_ADMIN) {
            $rules['institute_id'] = ['nullable', 'exists:institutes,id'];
        } else {
            // Institute admin cannot set institute_id - it's auto-set to their institute
            $rules['institute_id'] = ['prohibited'];
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $user = $this->user();
        
        // For institute admins, remove institute_id if provided
        if ($user && $user->role === User::ROLE_INSTITUTE) {
            $this->merge([
                'institute_id' => null,
            ]);
        }
    }
}
