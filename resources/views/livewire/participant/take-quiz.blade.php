<div>
    <x-slot:quizTitle>{{ $quiz->title }}</x-slot:quizTitle>
    <x-slot:progress>Question {{ $currentQuestionIndex + 1 }} of {{ $totalQuestions }}</x-slot:progress>
    
    <x-slot:timer>
        @if ($quiz->duration_minutes)
            <div x-data="{ 
                remaining: @entangle('remainingSeconds'),
                interval: null,
                mounted() {
                    this.interval = setInterval(() => {
                        if (this.remaining > 0) {
                            this.remaining--;
                        } else {
                            clearInterval(this.interval);
                            $wire.autoSubmit();
                        }
                    }, 1000);
                },
                formatTime() {
                    const hours = Math.floor(this.remaining / 3600);
                    const minutes = Math.floor((this.remaining % 3600) / 60);
                    const seconds = this.remaining % 60;
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }
            }" x-init="mounted()" class="flex items-center gap-2 px-4 py-2 rounded-lg" 
            :class="remaining < 300 ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'">
                <flux:icon name="clock" class="w-5 h-5" />
                <span class="font-mono text-lg font-semibold" x-text="formatTime()"></span>
            </div>
        @endif
    </x-slot:timer>

    @if ($currentQuestion)
        <div class="space-y-6">
            <!-- Question navigator -->
            <flux:card>
                <div class="flex flex-wrap gap-2">
                    @foreach ($questions as $index => $q)
                        <button 
                            wire:click="goToQuestion({{ $index }})"
                            class="w-10 h-10 rounded-lg font-semibold transition-all
                                {{ $index === $currentQuestionIndex ? 'bg-primary text-white ring-2 ring-primary ring-offset-2' : '' }}
                                {{ isset($answers[$q->id]) ? 'bg-green-100 text-green-700' : 'bg-zinc-100 text-zinc-600' }}
                                hover:scale-110">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>
                <div class="mt-3 text-sm text-zinc-600">
                    <span class="font-semibold">{{ $answeredCount }}</span> of {{ $totalQuestions }} answered
                </div>
            </flux:card>

            <!-- Question card -->
            <flux:card class="p-8">
                <div class="mb-6">
                    <flux:badge color="zinc" size="sm" class="mb-3">
                        {{ ucfirst(str_replace('_', ' ', $currentQuestion->question_type)) }}
                    </flux:badge>
                    <flux:heading size="xl" class="mb-2">
                        Question {{ $currentQuestionIndex + 1 }}
                    </flux:heading>
                    <flux:text class="text-lg">
                        {{ $currentQuestion->question_text }}
                    </flux:text>
                    <div class="mt-2 text-sm text-zinc-500">
                        {{ $currentQuestion->points }} {{ $currentQuestion->points === 1 ? 'point' : 'points' }}
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-3">
                    @foreach ($currentQuestion->options as $optionIndex => $option)
                        <label class="flex items-start gap-3 p-4 rounded-lg border-2 cursor-pointer transition-all
                            {{ isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] === $option->id 
                                ? 'border-primary bg-primary/5' 
                                : 'border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50' }}">
                            
                            @if ($currentQuestion->question_type === 'true_false')
                                <input 
                                    type="radio" 
                                    name="question_{{ $currentQuestion->id }}"
                                    wire:click="selectAnswer({{ $currentQuestion->id }}, {{ $option->id }})"
                                    {{ isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] === $option->id ? 'checked' : '' }}
                                    class="mt-1 rounded-full border-zinc-300 text-primary focus:ring-primary" />
                            @else
                                <input 
                                    type="radio" 
                                    name="question_{{ $currentQuestion->id }}"
                                    wire:click="selectAnswer({{ $currentQuestion->id }}, {{ $option->id }})"
                                    {{ isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] === $option->id ? 'checked' : '' }}
                                    class="mt-1 rounded border-zinc-300 text-primary focus:ring-primary" />
                            @endif
                            
                            <div class="flex-1">
                                <span class="font-semibold text-zinc-600 mr-2">{{ chr(65 + $optionIndex) }}.</span>
                                <span class="text-zinc-900">{{ $option->option_text }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </flux:card>

            <!-- Navigation buttons -->
            <div class="flex items-center justify-between">
                <flux:button 
                    wire:click="previousQuestion" 
                    variant="ghost"
                    :disabled="$currentQuestionIndex === 0">
                    ← Previous
                </flux:button>

                <div class="flex gap-3">
                    @if ($currentQuestionIndex === $totalQuestions - 1)
                        <flux:button 
                            wire:click="submitQuiz" 
                            variant="primary"
                            class="px-8">
                            Submit Quiz
                        </flux:button>
                    @else
                        <flux:button 
                            wire:click="nextQuestion" 
                            variant="primary">
                            Next →
                        </flux:button>
                    @endif
                </div>
            </div>
        </div>
    @else
        <flux:card class="text-center py-12">
            <flux:heading size="lg" class="mb-2">No Questions Available</flux:heading>
            <flux:text>This quiz doesn't have any questions yet.</flux:text>
        </flux:card>
    @endif
</div>
