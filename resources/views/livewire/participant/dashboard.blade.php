<div>
    <x-slot:header>My Dashboard</x-slot:header>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <flux:card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <flux:icon name="document-text" class="w-6 h-6 text-blue-600" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-600 mb-1">Available Quizzes</flux:text>
                    <flux:heading size="xl">{{ $availableQuizzes }}</flux:heading>
                </div>
            </div>
            <a href="{{ route('participant.quizzes') }}"
                class="text-sm text-primary hover:underline mt-3 inline-block">View all →</a>
        </flux:card>

        <flux:card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <flux:icon name="check-circle" class="w-6 h-6 text-green-600" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-600 mb-1">Completed Quizzes</flux:text>
                    <flux:heading size="xl">{{ $completedAttempts }}</flux:heading>
                </div>
            </div>
            <a href="{{ route('participant.results') }}"
                class="text-sm text-primary hover:underline mt-3 inline-block">View results →</a>
        </flux:card>

        <flux:card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                    <flux:icon name="star" class="w-6 h-6 text-purple-600" />
                </div>
                <div>
                    <flux:text class="text-sm text-zinc-600 mb-1">Average Score</flux:text>
                    <flux:heading size="xl">{{ number_format($averageScore, 1) }}</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Recent Activity -->
    @if ($recentAttempts->isNotEmpty())
        <flux:heading size="lg" class="mb-4">Recent Activity</flux:heading>
        <div class="space-y-3">
            @foreach ($recentAttempts as $attempt)
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <flux:heading size="base" class="mb-1">
                                {{ $attempt->quiz->title }}
                            </flux:heading>
                            <flux:text class="text-sm text-zinc-600">
                                {{ $attempt->submitted_at->diffForHumans() }}
                            </flux:text>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-xl font-bold 
                                            {{ $attempt->percentage >= 80 ? 'text-green-600' :
                    ($attempt->percentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </div>
                                <flux:text class="text-xs text-zinc-500">
                                    {{ $attempt->score }} / {{ $attempt->total_possible_score }} pts
                                </flux:text>
                            </div>

                            <flux:button href="{{ route('participant.quizzes.result', $attempt) }}" variant="ghost" size="sm">
                                View →
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @else
        <flux:card class="text-center py-8">
            <flux:heading size="lg" class="mb-2">No Activity Yet</flux:heading>
            <flux:text class="mb-4">Start taking quizzes to see your activity here!</flux:text>
            <flux:button href="{{ route('participant.quizzes') }}" variant="primary">
                Browse Quizzes
            </flux:button>
        </flux:card>
    @endif
</div>