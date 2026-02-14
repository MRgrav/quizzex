<div>
    {{-- Quiz View Page --}}

    @if (session('success'))
        <flux:card class="mb-6 bg-green-50 border border-green-200">
            <div class="flex items-center gap-2 text-green-700">
                <flux:icon name="check-circle" class="w-5 h-5" />
                <span>{{ session('success') }}</span>
            </div>
        </flux:card>
    @endif

    @if (session('error'))
        <flux:card class="mb-6 bg-red-50 border border-red-200">
            <div class="flex items-center gap-2 text-red-700">
                <flux:icon name="exclamation-circle" class="w-5 h-5" />
                <span>{{ session('error') }}</span>
            </div>
        </flux:card>
    @endif

    {{-- Live Quiz Indicator --}}
    @if ($quiz->isLive())
        <flux:card class="mb-6 bg-orange-50 border border-orange-200">
            <div class="flex items-center gap-3">
                <flux:icon name="clock" class="w-6 h-6 text-orange-600" />
                <div>
                    <flux:heading size="sm" class="text-orange-900">Quiz is Currently Live</flux:heading>
                    <flux:text class="text-orange-700 text-sm">
                        This quiz is active and cannot be edited.
                        Available until: {{ $quiz->end_time->format('M d, Y h:i A') }}
                    </flux:text>
                </div>
            </div>
        </flux:card>
    @elseif ($quiz->is_locked)
        <flux:card class="mb-6 bg-red-50 border border-red-200">
            <div class="flex items-center gap-3">
                <flux:icon name="lock-closed" class="w-6 h-6 text-red-600" />
                <div>
                    <flux:heading size="sm" class="text-red-900">Quiz is Locked</flux:heading>
                    <flux:text class="text-red-700 text-sm">
                        This quiz has been manually locked and cannot be edited.
                    </flux:text>
                </div>
            </div>
        </flux:card>
    @endif

    {{-- Quiz Details Card --}}
    <flux:card class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <flux:icon name="file-text" class="text-primary" />
                <flux:heading size="xl">Quiz Details</flux:heading>
            </div>
            <div class="flex gap-2">
                @if (!$editMode)
                    @if($this->canEdit())
                        <flux:button icon="pencil" wire:click="toggleEdit" variant="primary">
                            Edit
                        </flux:button>
                    @else
                        <flux:button icon="pencil" variant="primary" disabled>
                            Edit
                        </flux:button>
                    @endif
                @else
                    <flux:button wire:click="saveQuiz" variant="primary">
                        <flux:icon name="check" class="w-4 h-4" />
                        Save
                    </flux:button>
                    <flux:button wire:click="toggleEdit" variant="ghost">
                        <flux:icon name="x-mark" class="w-4 h-4" />
                        Cancel
                    </flux:button>
                @endif
            </div>
        </div>

        @if (!$editMode)
            {{-- View Mode --}}
            <div class="space-y-4">
                <div>
                    <flux:subheading class="text-zinc-500 mb-1">Title</flux:subheading>
                    <flux:heading size="lg" class="capitalize">{{ $quiz->title }}</flux:heading>
                </div>

                @if ($quiz->description)
                    <div>
                        <flux:subheading class="text-zinc-500 mb-1">Description</flux:subheading>
                        <flux:text>{{ $quiz->description }}</flux:text>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <flux:subheading class="text-zinc-500 mb-1">Institute</flux:subheading>
                        <flux:text>{{ $quiz->institute?->name ?? 'CSIR-NEIST (Jigyasa)' }}</flux:text>
                    </div>
                    <div>
                        <flux:subheading class="text-zinc-500 mb-1">Duration</flux:subheading>
                        <flux:text>{{ $quiz->duration_minutes ?? 'No limit' }}
                            {{ $quiz->duration_minutes ? 'minutes' : '' }}
                        </flux:text>
                    </div>
                    <div>
                        <flux:subheading class="text-zinc-500 mb-1">Status</flux:subheading>
                        <flux:badge rounded
                            color="{{ $quiz->status === 'active' ? 'green' : ($quiz->status === 'draft' ? 'yellow' : 'red') }}"
                            class="capitalize">
                            {{ $quiz->status }}
                        </flux:badge>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:subheading class="text-zinc-500 mb-1">Total Questions</flux:subheading>
                        <flux:text>{{ $quiz->total_questions }}</flux:text>
                    </div>
                    <div>
                        <flux:subheading class="text-zinc-500 mb-1">Total Points</flux:subheading>
                        <flux:text>{{ $quiz->total_points }}</flux:text>
                    </div>
                </div>
            </div>
        @else
            {{-- Edit Mode --}}
            <div class="space-y-4">
                <div>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="title" placeholder="Quiz title" />
                    @error('title') <flux:text class="text-red-600 text-sm">{{ $message }}</flux:text> @enderror
                </div>

                <div>
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model="description" placeholder="Quiz description (optional)" rows="3" />
                    @error('description') <flux:text class="text-red-600 text-sm">{{ $message }}</flux:text> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:label>Duration (minutes)</flux:label>
                        <flux:input type="number" wire:model="duration_minutes" placeholder="Leave empty for no limit" />
                        @error('duration_minutes') <flux:text class="text-red-600 text-sm">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    <div>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model="status">
                            @foreach(\App\Models\Quiz::STATUSES as $statusOption)
                                <flux:select.option value="{{ $statusOption }}">
                                    {{ ucfirst($statusOption) }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('status') <flux:text class="text-red-600 text-sm">{{ $message }}</flux:text> @enderror
                    </div>
                </div>
            </div>
        @endif
    </flux:card>

    {{-- Questions Card --}}
    <flux:card class="mb-6">
        <div class="flex items-center gap-2 mb-6">
            <flux:icon name="question-mark-circle" class="text-primary" />
            <flux:heading size="xl">Questions ({{ $quiz->questions->count() }})</flux:heading>
        </div>

        @if ($quiz->questions->count() > 0)
            <div class="space-y-4">
                @foreach ($quiz->questions as $index => $question)
                    <flux:card class="border">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <flux:badge>Question {{ $index + 1 }}</flux:badge>
                                    <flux:badge color="blue">{{ $question->points }}
                                        {{ $question->points === 1 ? 'point' : 'points' }}
                                    </flux:badge>
                                    <flux:badge color="purple" class="capitalize">
                                        {{ str_replace('_', ' ', $question->question_type) }}
                                    </flux:badge>
                                </div>
                                <div class="prose max-w-none">
                                    {!! $question->question_text !!}
                                </div>
                            </div>
                            <flux:button wire:click="deleteQuestion({{ $question->id }})" variant="ghost" size="sm"
                                wire:confirm="Are you sure you want to delete this question?">
                                <flux:icon name="trash" class="w-4 h-4 text-red-600" />
                            </flux:button>
                        </div>

                        @if ($question->explanation)
                            <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                                <flux:subheading class="text-blue-700 mb-1">Explanation</flux:subheading>
                                <flux:text class="text-blue-600">{{ $question->explanation }}</flux:text>
                            </div>
                        @endif

                        {{-- Options --}}
                        @if ($question->options->count() > 0)
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <flux:subheading>Answer Options</flux:subheading>
                                    <flux:button icon="{{ $showAnswers[$question->id] ?? false ? 'eye-slash' : 'eye' }}"
                                        wire:click="toggleAnswers({{ $question->id }})" variant="ghost" size="sm">
                                        {{ $showAnswers[$question->id] ?? false ? 'Hide' : 'Show' }} Answers
                                    </flux:button>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-2">
                                    @foreach ($question->options as $optionIndex => $option)
                                        <div
                                            class="flex items-center gap-2 p-2 rounded {{ ($showAnswers[$question->id] ?? false) && $option->is_correct ? 'bg-green-100 border border-green-200' : 'bg-zinc-100' }}">
                                            <span class="font-semibold text-zinc-600">{{ chr(65 + $optionIndex) }}.</span>
                                            <span class="flex-1">{{ $option->option_text }}</span>
                                            @if ($showAnswers[$question->id] ?? false)
                                                @if ($option->is_correct)
                                                    <flux:badge color="green">
                                                        <flux:icon name="check" class="w-3 h-3" />
                                                        Correct
                                                    </flux:badge>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </flux:card>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-zinc-500">
                <flux:icon name="question-mark-circle" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                <flux:text>No questions added yet. Add your first question below.</flux:text>
            </div>
        @endif
    </flux:card>

    {{-- Add Question Form --}}
    <flux:card>
        <div class="flex items-center gap-2 mb-6">
            <flux:icon name="plus-circle" class="text-primary" />
            <flux:heading size="xl">Add New Question</flux:heading>
        </div>

        <div class="space-y-4">
            <div>
                <flux:label>Question Text</flux:label>
                <div wire:ignore>
                    <input id="trix-question-input" type="hidden" wire:model="newQuestion.question_text">
                    <trix-editor input="trix-question-input" class="trix-content"></trix-editor>
                </div>
                @error('newQuestion.question_text') <flux:text class="text-red-600 text-sm">{{ $message }}</flux:text>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <flux:label>Question Type</flux:label>
                    <flux:select wire:model.live="newQuestion.question_type">
                        <flux:select.option value="multiple_choice">Multiple Choice</flux:select.option>
                        <flux:select.option value="true_false">True/False</flux:select.option>
                        <!-- <flux:select.option value="short_answer">Short Answer</flux:select.option> -->
                    </flux:select>
                    @error('newQuestion.question_type') <flux:text class="text-red-600 text-sm">{{ $message }}
                    </flux:text> @enderror
                </div>

                <div>
                    <flux:label>Points</flux:label>
                    <flux:input type="number" wire:model="newQuestion.points" min="1" />
                    @error('newQuestion.points') <flux:text class="text-red-600 text-sm">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>

            <div>
                <flux:label>Explanation (Optional)</flux:label>
                <flux:textarea wire:model="newQuestion.explanation" placeholder="Provide an explanation for the answer"
                    rows="2" />
                @error('newQuestion.explanation') <flux:text class="text-red-600 text-sm">{{ $message }}</flux:text>
                @enderror
            </div>

            {{-- Options --}}
            @if($newQuestion['question_type'])
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <flux:label>Answer Options</flux:label>
                        @if($newQuestion['question_type'] === 'multiple_choice')
                            <flux:button icon="plus" wire:click="addOption" variant="ghost" size="sm">
                                Add Option
                            </flux:button>
                        @endif
                    </div>

                    <div class="space-y-3">
                        @foreach ($newQuestion['options'] as $index => $option)
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-zinc-600 w-6">{{ chr(65 + $index) }}.</span>

                                @if($newQuestion['question_type'] === 'true_false')
                                    {{-- For True/False, show read-only options --}}
                                    <flux:input wire:model="newQuestion.options.{{ $index }}.option_text" class="flex-1" disabled />
                                @else
                                    {{-- For MCQ, allow editing --}}
                                    <flux:input wire:model="newQuestion.options.{{ $index }}.option_text" placeholder="Option text"
                                        class="flex-1" />
                                @endif

                                <label
                                    class="flex items-center gap-2 px-3 py-2 bg-zinc-50 rounded-lg cursor-pointer hover:bg-zinc-100">
                                    @if($newQuestion['question_type'] === 'true_false')
                                        <input type="radio" wire:click="selectTrueFalseAnswer({{ $index }})"
                                            @if($option['is_correct']) checked @endif name="true_false_answer"
                                            class="rounded-full border-zinc-300 text-green-600 focus:ring-green-500" />
                                    @else
                                        <input type="checkbox" wire:model="newQuestion.options.{{ $index }}.is_correct"
                                            class="rounded border-zinc-300 text-green-600 focus:ring-green-500" />
                                    @endif
                                    <span class="text-sm text-zinc-700">Correct</span>
                                </label>

                                @if ($newQuestion['question_type'] === 'multiple_choice' && count($newQuestion['options']) > 2)
                                    <flux:button wire:click="removeOption({{ $index }})" variant="ghost" size="sm">
                                        <flux:icon name="trash" class="w-4 h-4 text-red-600" />
                                    </flux:button>
                                @endif
                            </div>
                            @error("newQuestion.options.{$index}.option_text") <flux:text class="text-red-600 text-sm ml-8">
                                {{ $message }}
                            </flux:text> @enderror
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex justify-end gap-2 pt-4 border-t">
                <flux:button icon="plus" wire:click="addQuestion" variant="primary">
                    Add Question
                </flux:button>
            </div>
        </div>
    </flux:card>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Trix editor integration with Livewire
            const trixEditor = document.querySelector('trix-editor');

            if (trixEditor) {
                // Update Livewire when Trix content changes
                trixEditor.addEventListener('trix-change', function (e) {
                    @this.set('newQuestion.question_text', e.target.value);
                });

                // Listen for Livewire updates to reset the editor
                Livewire.on('question-added', () => {
                    trixEditor.editor.loadHTML('');
                });
            }
        });
    </script>
@endpush