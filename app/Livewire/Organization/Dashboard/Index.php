<?php

namespace App\Livewire\Organization\Dashboard;

use App\Models\User;
use App\Services\InstituteService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.organization')]
#[Title('Dashboard')]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function render(InstituteService $service)
    {
        // 1. Authorize
        $this->authorize('viewAny', User::class);

        // 2. Get Current Institute
        $institute = \Auth::user()->institute;

        // 3. Fetch Data via Service
        $participants = $service->getParticipants($institute, [
            'search' => $this->search
        ]);

        // 4. Get Quiz Statistics for this institute
        $quizService = app(\App\Services\QuizService::class);
        $quizStats = $quizService->getQuizStatsByInstitute($institute->id);

        return view('livewire.organization.dashboard.index', [
            'participants' => $participants,
            'quizStats' => $quizStats,
        ]);
    }
}
