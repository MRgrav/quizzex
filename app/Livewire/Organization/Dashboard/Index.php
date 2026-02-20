<?php

namespace App\Livewire\Organization\Dashboard;

use App\Models\Quiz;
use App\Models\User;
use App\Models\QuizAttempt;
use App\Services\InstituteService;
use Livewire\Attributes\Computed;
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

    #[Computed(persist: true, seconds: 1200)]
    public function render(InstituteService $service)
    {
        // 1. Authorize
        $this->authorize('viewAny', User::class);

        // 2. Get Current Institute
        $institute = \Auth::user()->institute;

        // 3. Fetch Data via Service
        $participants = User::where('institute_id', $institute->id)
            ->where('role', USER::ROLE_PARTICIPANT)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        // 4. Get Quiz Statistics for this institute
        // $stats = \Illuminate\Support\Facades\Cache::remember('org_dashboard_stats_' . $institute->id, 300, function () use ($institute) {
        //     $quizService = app(\App\Services\QuizService::class);
        //     return array_merge($quizService->getQuizStatsByInstitute($institute->id), [
        //         'total_participants_global' => \App\Models\User::where('role', 'participant')->count(),
        //         'total_quizzes_global' => \App\Models\Quiz::count(),
        //     ]);
        // });

        $stats = [
            'total_participants' => \App\Models\User::where('role', 'participant')->where('institute_id', $institute->id)->count(),
            'total_quizzes' => \App\Models\Quiz::where('institute_id', $institute->id)->count(),
            'active_quizzes' => \App\Models\Quiz::where('institute_id', $institute->id)->where('status', 'active')->count(),
            'quiz_attempts' => QuizAttempt::whereHas('quiz', fn($q) => $q->where('institute_id', $institute->id))->count(),
        ];

        $recentQuizzes = Quiz::where('institute_id', $institute->id)->orderBy('created_at', 'desc')->paginate(5);

        return view('livewire.organization.dashboard.index', [
            'participants' => $participants,
            'stats' => $stats,
            'recentQuizzes' => $recentQuizzes,
        ]);
    }
}
