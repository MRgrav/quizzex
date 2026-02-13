<?php

namespace App\Livewire\Forms\Quiz;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CreateQuizForm extends Form
{
    public ?string $title = '';
    public ?string $description = '';
    public ?string $status = ''; // Default to one of your STATUSES
    public ?int $duration_minutes = null;
    public ?int $institute_id = null; // Only actionable by Admins
    public ?string $start_time = null;
    public ?string $end_time = null;

    public function rules()
    {
        $user = auth()->user();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'string', Rule::in(Quiz::STATUSES)],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'start_time' => ['nullable', 'date', 'after_or_equal:now'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
        ];

        // Replicating your StoreQuizRequest logic
        if ($user->role === User::ROLE_ADMIN) {
            $rules['institute_id'] = ['nullable', 'exists:institutes,id'];
        } else {
            // Institute admins cannot submit an institute_id via the form
            // We don't strictly need 'prohibited' here if we don't bind the input in the view, 
            // but it's good security practice.
            $rules['institute_id'] = ['prohibited'];
        }

        return $rules;
    }
}
