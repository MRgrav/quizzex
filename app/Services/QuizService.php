<?php
namespace App\Services;

use App\Models\Question;
use App\Models\Quiz;

class QuizService
{
    public function createQuiz($data)
    {
        return Quiz::create($data);
    }

    public function updateQuiz()
    {

    }

    public function listAll()
    {
        return Quiz::get();
    }

    public function showQuiz($id)
    {
        return Quiz::findOrFail($id);
    }

    public function addQuizQuestion($data)
    {
        return Question::create($data);
    }

    /**
     * Get quiz statistics for a specific institute
     */
    public function getQuizStatsByInstitute($instituteId)
    {
        return [
            'total_quizzes' => Quiz::where('institute_id', $instituteId)->count(),
            'active_quizzes' => Quiz::where('institute_id', $instituteId)
                ->where('status', 'active')
                ->count(),
            'draft_quizzes' => Quiz::where('institute_id', $instituteId)
                ->where('status', 'draft')
                ->count(),
            'total_questions' => Question::whereHas('quiz', function ($query) use ($instituteId) {
                $query->where('institute_id', $instituteId);
            })->count(),
        ];
    }

    /**
     * Get global quiz statistics (for admin dashboard)
     */
    public function getGlobalQuizStats()
    {
        return [
            'total_quizzes' => Quiz::count(),
            'active_quizzes' => Quiz::where('status', 'active')->count(),
            'draft_quizzes' => Quiz::where('status', 'draft')->count(),
            'total_questions' => Question::count(),
            'csir_quizzes' => Quiz::whereNull('institute_id')->count(),
        ];
    }
}