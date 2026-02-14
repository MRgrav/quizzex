<?php

namespace App\Livewire\Organization\Participants;

use App\Models\User;
use App\Services\ParticipantService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.organization')]
#[Title('Participants')]
class Index extends Component
{
    use WithPagination;

    public $showForm = false;
    public $name = '';
    public $email = '';
    public $password = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ];

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if (!$this->showForm) {
            $this->reset(['name', 'email', 'password']);
            $this->resetValidation();
        }
    }

    public function addParticipant(ParticipantService $service)
    {
        $this->validate();

        $institute = Auth::user()->institute;

        $service->addParticipant([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ], $institute->id);

        session()->flash('success', 'Participant added successfully!');
        $this->reset(['name', 'email', 'password', 'showForm']);
        $this->resetValidation();
    }

    public function render(ParticipantService $service)
    {
        $institute = Auth::user()->institute;
        $participants = $service->listInstitutesParticipant($institute);
        $totalParticipants = User::where('institute_id', $institute->id)
            ->where('role', User::ROLE_PARTICIPANT)
            ->count();

        return view('livewire.organization.participants.index', [
            'participants' => $participants,
            'totalParticipants' => $totalParticipants
        ]);
    }
}
