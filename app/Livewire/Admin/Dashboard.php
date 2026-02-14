<?php

namespace App\Livewire\Admin;

use App\Models\Quiz;
use App\Services\InstituteService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    public function render(InstituteService $service)
    {
        $institutes = $service->getPaginated(5);

        $quizService = app(\App\Services\QuizService::class);
        $quizStats = $quizService->getGlobalQuizStats();

        $recentQuizzes = Quiz::orderBy('created_at', 'desc')->paginate(5);

        return view('livewire.admin.dashboard', [
            'institutes' => $institutes,
            'quizStats' => $quizStats,
            'recentQuizzes' => $recentQuizzes,
        ]);
    }
}
