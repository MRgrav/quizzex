<?php

namespace App\Livewire\Shared;

use App\Livewire\Forms\Quiz\CreateQuizForm;
use App\Models\Quiz;
use App\Models\User;
use App\Services\QuizService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Create Quiz')]
class CreateQuiz extends Component
{
    public CreateQuizForm $form;
    public string $layout = 'layouts.admin';
    public string $redirectRoute = 'admin.quizzes';

    public function mount()
    {
        // Detect user role and set appropriate layout and redirect
        $user = auth()->user();

        if ($user->role === User::ROLE_INSTITUTE) {
            $this->layout = 'layouts.organization';
            $this->redirectRoute = 'organization.quizzes';
        }
    }

    public function create()
    {
        $service = app(QuizService::class);

        // 1. Authorization
        $this->authorize('create', Quiz::class);

        // 2. Validation
        $this->form->validate();

        // 3. Create quiz
        try {
            $user = auth()->user();
            $data = $this->form->all();
            $data['created_by'] = $user->id;

            // Auto-assign institute for organization users
            if ($user->role === User::ROLE_INSTITUTE) {
                $data['institute_id'] = $user->institute_id;
            }

            $quiz = $service->createQuiz($data);

            session()->flash('message', 'Quiz created successfully. Now add questions.');

            // Redirect to question management page
            $questionRoute = $user->role === User::ROLE_INSTITUTE
                ? 'organization.quizzes.questions'
                : 'admin.quizzes.questions';

            return $this->redirect(route($questionRoute, $quiz));

        } catch (\Exception $e) {
            \Log::error('Quiz Creation Failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'data_attempted' => $this->form->all()
            ]);

            $this->addError('root', 'Something went wrong while creating the quiz. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.shared.create-quiz')
            ->layout($this->layout);
    }
}
