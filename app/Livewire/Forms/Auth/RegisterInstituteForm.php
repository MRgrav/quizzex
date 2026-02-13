<?php

namespace App\Livewire\Forms\Auth;

use App\Models\Institute;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterInstituteForm extends Form
{
    #[Validate(['required', 'string', 'max:255'])]
    public $name = "";
    #[Validate(['required', 'string', 'email', 'max:255', 'unique:users'])]
    public $email = "";
    // #[Validate(['required', 'string', 'confirmed', Password::defaults()])]
    public $password = "";
    public $password_confirmation = "";
    #[Validate(['required', 'string', 'max:255'])]
    public $institute_name = "";
    // #[Validate(['required', 'string', 'in:' . implode(',', Institute::TYPES)])]
    public $type = "";
    #[Validate(['nullable', 'string', 'max:1000'])]
    public $address = "";
    #[Validate(['required', 'string', 'max:255'])]
    public $contact_person = "";
    #[Validate(['required', 'string', 'max:20'])]
    public $phone = "";

    // 2. Add this method to handle dynamic rules like Password::defaults()
    public function rules()
    {
        return [
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::defaults() // Now this is valid PHP!
            ],
            'type' => [
                'required',
                'string',
                'in:' . implode(',', Institute::TYPES)
            ]
        ];
    }

    // Helpers to separate data for the two services
    public function instituteData()
    {
        return [
            'name' => $this->institute_name,
            'type' => $this->type,
            'address' => $this->address,
            'contact_person' => $this->contact_person,
            'phone' => $this->phone,
        ];
    }

    public function adminData()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
