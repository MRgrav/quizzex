<div>
    <x-slot:header>Quiz Result</x-slot:header>

    <!-- Score Summary Card -->
    <flux:card class="mb-6 bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20">
        <div class="text-center py-8">
            <flux:heading size="2xl" class="mb-4">{{ $quiz->title }}</flux:heading>
            
            <div class="flex items-center justify-center gap-8 mb-6">
                <!-- Score -->
                <div>
                    <div class="text-5xl font-bold text-primary mb-2">
                        {{ number_format($quizAttempt->percentage, 1) }}%
                    </div>
                    <flux:text class="text-zinc-600">
                        {{ $quizAttempt->score }} / {{ $quizAttempt->total_possible_score }} points
                    </flux:text>
                </div>

                <!-- Time Taken -->
                @if ($timeTaken)
                    <div class="border-l-2 border-zinc-300 pl-8">
                        <div class="text-3xl font-bold text-zinc-700 mb-2">
                            {{ $timeTaken }} min
                        </div>
                        <flux:text class="text-zinc-600">Time Taken</flux:text>
                    </div>
                @endif
            </div>

            <!-- Pass/Fail Badge -->
            @if ($quizAttempt->percentage >= 60)
                <flux:badge color="green" size="lg" class="text-lg px-6 py-2">
                    ✅ Passed
                </flux:badge>
            @else
                <flux:badge color="red" size="lg" class="text-lg px-6 py-2">
                    ❌ Not Passed
                </flux:badge>
            @endif
        </div>
    </flux:card>

    <!-- Question-by-Question Review -->
    <flux:heading size="lg" class="mb-4">Question Review</flux:heading>

    <div class="space-y-4">
        @foreach ($answers as $index => $answer)
            @php
                $question = $answer->question;
                $correctOptions = $question->correctOptions;
                $selectedOption = $answer->option;
            @endphp

            <flux:card class="{{ $answer->is_correct ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500' }}">
                <div class="flex items-start gap-4">
                    <!-- Question Number Badge -->
                    <div class="flex-shrink-0">
                        @if ($answer->is_correct)
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold">
                                ✓
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-bold">
                                ✗
                            </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <!-- Question -->
                        <div class="mb-3">
                            <flux:badge color="zinc" size="sm" class="mb-2">
                                Question {{ $index + 1 }}
                            </flux:badge>
                            <flux:text class="text-lg font-semibold text-zinc-900">
                                {!! $question->question_text !!}
                            </flux:text>
                            <flux:text class="text-sm text-zinc-500 mt-1">
                                {{ $question->points }} {{ $question->points === 1 ? 'point' : 'points' }}
                            </flux:text>
                        </div>

                        <!-- Your Answer -->
                        <div class="mb-2">
                            <flux:text class="text-sm font-semibold text-zinc-600 mb-1">Your Answer:</flux:text>
                            <div class="p-3 rounded-lg {{ $answer->is_correct ? 'bg-green-50' : 'bg-red-50' }}">
                                <flux:text class="{{ $answer->is_correct ? 'text-green-900' : 'text-red-900' }}">
                                    {{ $selectedOption?->option_text ?? 'No answer selected' }}
                                </flux:text>
                            </div>
                        </div>

                        <!-- Correct Answer (if wrong) -->
                        @if (!$answer->is_correct && $correctOptions->isNotEmpty())
                            <div class="mb-2">
                                <flux:text class="text-sm font-semibold text-zinc-600 mb-1">Correct Answer:</flux:text>
                                <div class="p-3 rounded-lg bg-green-50">
                                    <flux:text class="text-green-900">
                                        {{ $correctOptions->first()->option_text }}
                                    </flux:text>
                                </div>
                            </div>
                        @endif

                        <!-- Explanation -->
                        @if ($question->explanation)
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <flux:text class="text-sm font-semibold text-blue-900 mb-1">Explanation:</flux:text>
                                <flux:text class="text-blue-800">
                                    {!! $question->explanation !!}
                                </flux:text>
                            </div>
                        @endif

                        <!-- Points Earned -->
                        <div class="mt-3 text-sm text-zinc-600">
                            Earned: <span class="font-semibold">{{ $answer->points_earned }} / {{ $question->points }}</span> points
                        </div>
                    </div>
                </div>
            </flux:card>
        @endforeach
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex gap-4">
        <flux:button href="{{ route((auth()->user()->role === 'institute' ? 'organization' : auth()->user()->role) . '.quizzes') }}" variant="primary">
            ← Back to Quizzes
        </flux:button>
        <flux:button href="{{ route((auth()->user()->role === 'institute' ? 'organization' : auth()->user()->role) . '.results') }}" variant="ghost">
            View All Results
        </flux:button>
    </div>
</div>
