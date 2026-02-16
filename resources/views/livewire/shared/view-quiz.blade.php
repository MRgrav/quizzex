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
    <flux:card>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <flux:icon name="question-mark-circle" class="text-primary" />
                <flux:heading size="xl">Questions ({{ $quiz->questions->count() }})</flux:heading>
            </div>
            <flux:button
                href="{{ auth()->user()->role === 'admin' ? route('admin.quizzes.questions', $quiz) : route('organization.quizzes.questions', $quiz) }}"
                variant="primary" icon="pencil">
                Manage Questions
            </flux:button>
        </div>

        @if ($quiz->questions->count() > 0)
            <flux:text class="text-zinc-600">
                This quiz has {{ $quiz->questions->count() }} question{{ $quiz->questions->count() === 1 ? '' : 's' }}
                totaling {{ $quiz->total_points }} point{{ $quiz->total_points === 1 ? '' : 's' }}.
                Click "Manage Questions" to add, edit, or remove questions.
            </flux:text>
        @else
            <div class="text-center py-8 text-zinc-500">
                <flux:icon name="question-mark-circle" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                <flux:text class="mb-4">No questions added yet.</flux:text>
                <flux:button
                    href="{{ auth()->user()->role === 'admin' ? route('admin.quizzes.questions', $quiz) : route('organization.quizzes.questions', $quiz) }}"
                    variant="primary" icon="plus">
                    Add Questions
                </flux:button>
            </div>
        @endif
    </flux:card>
</div>