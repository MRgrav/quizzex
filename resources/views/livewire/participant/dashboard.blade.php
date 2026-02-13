<div>
    <x-slot:header>My Dashboard</x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Available Quizzes</h3>
            <p class="text-3xl font-bold text-primary">0</p>
            <a href="{{ route('participant.quizzes') }}"
                class="text-sm text-primary hover:underline mt-2 inline-block">View all →</a>
        </div>

        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Completed Quizzes</h3>
            <p class="text-3xl font-bold text-green-600">0</p>
            <a href="{{ route('participant.results') }}"
                class="text-sm text-primary hover:underline mt-2 inline-block">View results →</a>
        </div>
    </div>
</div>