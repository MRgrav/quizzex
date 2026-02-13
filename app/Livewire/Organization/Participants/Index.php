<?php

namespace App\Livewire\Organization\Participants;

use App\Models\User;
use App\Services\ParticipantService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.organization')]
#[Title('Participants')]
class Index extends Component
{
    public $totalParticipants = 0;

    public function __construct()
    {
        $this->totalParticipants = User::where('institute_id', Auth::user()->institute->id)->count();
    }

    public function render(ParticipantService $service)
    {
        $institute = Auth::user()->institute;
        $participants = $service->listInstitutesParticipant($institute);
        return view('livewire.organization.participants.index', ['participants' => $participants, 'totalParticipants' => $this->totalParticipants]);
    }
}
