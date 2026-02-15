<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use App\Services\InstituteService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    #[Computed(persist: true, seconds: 1200)]
    public function render(InstituteService $service)
    {
        $institutes = $service->getPaginated(5);

        // $stats = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 600, function () {
        //     $quizService = app(\App\Services\QuizService::class);
        //     return array_merge($quizService->getGlobalQuizStats(), [
        //         'total_institutes' => \App\Models\Institute::count(),
        //         'pending_institutes' => \App\Models\Institute::where('status', 'pending')->count(),
        //         'total_participants' => \App\Models\User::where('role', 'participant')->count(),
        //     ]);
        // });

        $quizService = app(\App\Services\QuizService::class);
        $stats = array_merge($quizService->getGlobalQuizStats(), [
            'total_institutes' => \App\Models\Institute::count(),
            'pending_institutes' => \App\Models\Institute::where('status', 'pending')->count(),
            'total_participants' => \App\Models\User::where('role', 'participant')->count(),
        ]);

        $recentQuizzes = Quiz::orderBy('created_at', 'desc')->paginate(5);

        return view('livewire.admin.dashboard', [
            'institutes' => $institutes,
            'stats' => $stats,
            'recentQuizzes' => $recentQuizzes,
        ]);
    }
}
