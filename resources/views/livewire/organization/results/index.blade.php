<div>
    <x-slot:header>My Results</x-slot:header>

    @if ($attempts->isEmpty())
        <flux:card class="text-center py-12">
            <flux:icon name="document-text" class="w-16 h-16 mx-auto text-zinc-400 mb-4" />
            <flux:heading size="lg" class="mb-2">No Results Yet</flux:heading>
        </flux:card>
    @else
        <div class="space-y-4">
            @foreach ($attempts as $attempt)
                <flux:card class="hover:shadow-lg transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <flux:text>
                                {{ $attempt->user->name }}
                            </flux:text>
                            <flux:heading size="lg" class="mb-1">
                                {{ $attempt->quiz->title }}
                            </flux:heading>
                            <div class="flex items-center gap-4 text-sm text-zinc-600">
                                <span>{{ $attempt->submitted_at->format('M d, Y h:i A') }}</span>
                                <span>•</span>
                                <span>{{ $attempt->submitted_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <!-- Score Display -->
                            <div class="text-center">
                                <div class="text-3xl font-bold 
                                                            {{ $attempt->percentage >= 80 ? 'text-green-600' :
                    ($attempt->percentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($attempt->percentage, 1) }}%
                                </div>
                                <flux:text class="text-xs text-zinc-500">
                                    {{ $attempt->score }} / {{ $attempt->total_possible_score }} pts
                                </flux:text>
                            </div>

                            <!-- Status Badge -->
                            @if ($attempt->percentage >= 60)
                                <flux:badge color="green" size="sm">✅ Passed</flux:badge>
                            @else
                                <flux:badge color="red" size="sm">❌ Not Passed</flux:badge>
                            @endif

                            <!-- View Details Button -->
                            <flux:button href="{{ route('organization.quizzes.result', $attempt) }}" variant="ghost" size="sm">
                                View Details →
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $attempts->links() }}
        </div>
    @endif
</div>