<div>
    <x-slot:header>Available Quizzes</x-slot:header>

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if ($quizzes->isEmpty())
        <flux:card class="text-center py-12">
            <flux:icon name="document-text" class="w-16 h-16 mx-auto text-zinc-400 mb-4" />
            <flux:heading size="lg" class="mb-2">No Quizzes Available</flux:heading>
            <flux:text>There are no quizzes available at the moment. Check back later!</flux:text>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($quizzes as $quiz)
                @php
                    $hasCompleted = $quiz->attempts->where('status', 'submitted')->isNotEmpty();
                    $hasInProgress = $quiz->attempts->where('status', 'in_progress')->isNotEmpty();
                    $isLive = $quiz->isLive();
                    $isUpcoming = $quiz->isUpcoming();
                    $isCompleted = $quiz->isCompleted();
                @endphp

                <flux:card class="hover:shadow-lg transition-shadow">
                    <div class="flex flex-col h-full">
                        <!-- Header with status badge -->
                        <div class="flex items-start justify-between mb-3">
                            <flux:heading size="lg" class="flex-1">{{ $quiz->title }}</flux:heading>
                            @if ($isLive)
                                <flux:badge color="green" size="sm">🟢 Live</flux:badge>
                            @elseif ($isUpcoming)
                                <flux:badge color="yellow" size="sm">🟡 Upcoming</flux:badge>
                            @elseif ($isCompleted)
                                <flux:badge color="zinc" size="sm">⚫ Ended</flux:badge>
                            @endif
                        </div>

                        <!-- Description -->
                        @if ($quiz->description)
                            <flux:text class="text-sm text-zinc-600 mb-4 line-clamp-2">
                                {{ $quiz->description }}
                            </flux:text>
                        @endif

                        <!-- Quiz metadata -->
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex items-center gap-2 text-zinc-600">
                                <flux:icon name="clock" class="w-4 h-4" />
                                <span>{{ $quiz->duration_minutes }} minutes</span>
                            </div>
                            <div class="flex items-center gap-2 text-zinc-600">
                                <flux:icon name="document-text" class="w-4 h-4" />
                                <span>{{ $quiz->total_questions }} questions</span>
                            </div>
                            <div class="flex items-center gap-2 text-zinc-600">
                                <flux:icon name="star" class="w-4 h-4" />
                                <span>{{ $quiz->total_points }} points</span>
                            </div>
                        </div>

                        <!-- Availability window -->
                        @if ($quiz->start_time && $quiz->end_time)
                            <div class="text-xs text-zinc-500 mb-4 p-2 bg-zinc-50 rounded">
                                <div>Available: {{ $quiz->start_time->format('M d, Y h:i A') }}</div>
                                <div>Until: {{ $quiz->end_time->format('M d, Y h:i A') }}</div>
                            </div>
                        @endif

                        <!-- Action button -->
                        <div class="mt-auto">
                            @if ($hasCompleted)
                                <flux:button variant="ghost" disabled class="w-full">
                                    ✅ Completed
                                </flux:button>
                            @elseif ($hasInProgress)
                                <flux:button wire:click="startQuiz({{ $quiz->id }})" variant="primary" class="w-full">
                                    Continue Quiz →
                                </flux:button>
                            @elseif ($isLive)
                                <flux:button wire:click="startQuiz({{ $quiz->id }})" variant="primary" class="w-full">
                                    Start Quiz
                                </flux:button>
                            @elseif ($isUpcoming)
                                <flux:button variant="ghost" disabled class="w-full">
                                    Not Yet Available
                                </flux:button>
                            @else
                                <flux:button variant="ghost" disabled class="w-full">
                                    Quiz Ended
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    <div class="mt-6">
        {{ $quizzes->links() }}
    </div>
</div>