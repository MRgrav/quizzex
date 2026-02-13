<?php

namespace App\Livewire\Auth;

use App\Livewire\Forms\Auth\RegisterInstituteForm;
use App\Models\Institute;
use App\Models\User;
use App\Services\InstituteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Institute Registration')]
class InstituteRegister extends Component
{
    public RegisterInstituteForm $form;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $institute_name = '';
    public $type = '';
    public $address = '';
    public $contact_person = '';
    public $phone = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'institute_name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:school,college,university,training_center'],
            'address' => ['required', 'string', 'max:1000'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }

    public function register(InstituteService $service)
    {
        $this->form->validate();

        try {
            DB::beginTransaction();

            // Create user account with institute role
            $user = $service->createInstituteAdmin(
                $this->form->adminData()
            );

            $institute = $service->createInstitute(
                $user,
                $this->form->instituteData()
            );

            $user->institute_id = $institute->id;
            $user->save();
            // $user = User::create([
            //     'name' => $this->name,
            //     'email' => $this->email,
            //     'password' => Hash::make($this->password),
            //     'role' => 'institute',
            //     'status' => 'pending',
            // ]);

            // Create institute record
            // Institute::create([
            //     'name' => $this->institute_name,
            //     'type' => $this->type,
            //     'address' => $this->address,
            //     'contact_person' => $this->contact_person,
            //     'phone' => $this->phone,
            //     'user_id' => $user->id,
            //     'status' => 'pending',
            // ]);

            DB::commit();

            session()->flash('message', 'Institute registration successful! Please wait for admin approval.');
            $this->reset();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            session()->flash('error', 'Registration failed. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.auth.institute-register');
    }
}
