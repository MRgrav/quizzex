<div>
    <x-slot:header>Dashboard</x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Participants</h3>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Quizzes</h3>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Quiz Attempts</h3>
            <p class="text-3xl font-bold text-yellow-600">0</p>
        </div>
    </div>

    <div class="mt-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="flex gap-4">
                <a href="{{ route('institute.participants') }}" class="btn-primary">Add Participant</a>
                <a href="{{ route('institute.quizzes') }}" class="btn-primary">Create Quiz</a>
            </div>
        </div>
    </div>
</div>