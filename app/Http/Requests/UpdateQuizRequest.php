<?php

namespace App\Http\Requests;

use App\Models\Institute;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Admin can update any quiz, institute can only update their own.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $quiz = $this->route('quiz');
        
        if (!$user || !$quiz) {
            return false;
        }

        // Admin can update any quiz
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        // Institute admin can only update quizzes from their own institute
        if ($user->role === User::ROLE_INSTITUTE) {
            $institute = $user->institute;
            if (!$institute || $institute->status !== Institute::STATUS_APPROVED) {
                return false;
            }
            // Quiz must belong to their institute
            return $quiz->institute_id === $institute->id;
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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'required', 'string', Rule::in(Quiz::STATUSES)],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
        ];

        // Admin can change institute_id, institute cannot
        if ($user->role === User::ROLE_ADMIN) {
            $rules['institute_id'] = ['nullable', 'exists:institutes,id'];
        } else {
            $rules['institute_id'] = ['prohibited'];
        }

        return $rules;
    }
}
