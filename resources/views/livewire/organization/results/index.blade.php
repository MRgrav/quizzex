<div>
    {{-- Search & Sort Bar --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search"
                placeholder="Search by participant name or quiz title..." icon="magnifying-glass" clearable />
        </div>
        <div class="flex items-center gap-2">
            <flux:select wire:model.live="sortBy" class="w-44">
                <flux:select.option value="submitted_at">Date</flux:select.option>
                <flux:select.option value="participant_name">Participant Name</flux:select.option>
                <flux:select.option value="quiz_title">Quiz Title</flux:select.option>
                <flux:select.option value="percentage">Score</flux:select.option>
            </flux:select>
            <flux:button wire:click="sort('{{ $sortBy }}')" variant="ghost" size="sm" class="px-3"
                title="Toggle sort direction">
                @if ($sortDir === 'asc')
                    <flux:icon name="arrow-up" class="w-4 h-4" />
                @else
                    <flux:icon name="arrow-down" class="w-4 h-4" />
                @endif
            </flux:button>
        </div>
    </div>

    @if ($attempts->isEmpty())
        <flux:card class="text-center py-12">
            <flux:icon name="document-text" class="w-16 h-16 mx-auto text-zinc-400 mb-4" />
            <flux:heading size="lg" class="mb-2">
                {{ $search ? 'No results found for "' . $search . '"' : 'No Results Yet' }}
            </flux:heading>
            @if ($search)
                <flux:button wire:click="$set('search', '')" variant="ghost" size="sm">Clear search</flux:button>
            @endif
        </flux:card>
    @else
        <div class="space-y-3">
            @foreach ($attempts as $attempt)
                <flux:card class="hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between gap-4">
                        {{-- Left: Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <flux:icon name="user" class="w-4 h-4 text-zinc-400 flex-shrink-0" />
                                <flux:text class="font-medium text-zinc-800 truncate">
                                    {{ $attempt->participant->name }}
                                </flux:text>
                            </div>
                            <flux:heading size="base" class="mb-1 truncate">
                                {{ $attempt->quiz->title }}
                            </flux:heading>
                            <div class="flex items-center gap-3 text-xs text-zinc-500">
                                <span>{{ $attempt->submitted_at->format('d M Y, h:i A') }}</span>
                                <span>•</span>
                                <span>{{ $attempt->submitted_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Right: Score + Badge + Action --}}
                        <div class="flex items-center gap-5 flex-shrink-0">
                            <div class="text-center">
                                <div
                                    class="text-2xl font-bold
                                                                    {{ $attempt->percentage >= 80 ? 'text-green-600' : ($attempt->percentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </div>
                                <flux:text class="text-xs text-zinc-500">
                                    {{ $attempt->score }} / {{ $attempt->total_possible_score }} pts
                                </flux:text>
                            </div>

                            @if ($attempt->percentage >= 60)
                                <flux:badge color="green" size="sm">Passed</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">Failed</flux:badge>
                            @endif

                            <flux:button href="{{ route('organization.quizzes.result', $attempt) }}" variant="ghost" size="sm">
                                View →
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $attempts->links() }}
        </div>
    @endif
</div>