<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreParticipantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only institute admins can create participants
        $user = $this->user();
        
        if (!$user || $user->role !== User::ROLE_INSTITUTE) {
            return false;
        }

        // Ensure the institute is approved
        $institute = $user->institute;
        if (!$institute || $institute->status !== 'approved') {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            // Explicitly prevent institute_id from being set by the user
            'institute_id' => ['prohibited'],
        ];
    }

    /**
     * Prepare the data for validation.
     * Remove any attempt to set institute_id from the request.
     */
    protected function prepareForValidation(): void
    {
        // Remove institute_id if somehow it was included in the request
        $this->merge([
            'institute_id' => null,
        ]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'A participant with this email already exists.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
