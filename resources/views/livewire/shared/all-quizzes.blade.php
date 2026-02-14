<div>
    {{-- Do what you can, with what you have, where you are. - Theodore Roosevelt --}}
    <flux:card class="flex gap-3 items-center bg-white shadow-card p-4 flex-wrap lg:flex-nowrap gap-4 mb-4">
        <div class="grow flex-1 min-w-[300px] w-full md:w-64">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search name..." />
        </div>

        <flux:select wire:model.live="status" placeholder="All Statuses" class="w-40">
            <flux:select.option value="active">Active</flux:select.option>
            <flux:select.option value="pending">Pending</flux:select.option>
            <flux:select.option value="inactive">Inactive</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="type" placeholder="All Types" class="w-40">
            <flux:select.option value="school">School</flux:select.option>
            <flux:select.option value="college">College</flux:select.option>
        </flux:select>
    </flux:card>
    <flux:card class="bg-white shadow-card hover-levitate p-0 overflow-hidden border-none capitalize">
        <flux:table class="w-full">
            <flux:table.columns>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Total Questions</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Created At</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($quizzes as $quiz)
                    <flux:table.row :key="$quiz->id">
                        <flux:table.cell>
                            <flux:text class="font-bold text-zinc-900">{{ $quiz->title }}</flux:text>
                            <flux:text class="text-zinc-500 text-sm">{{ $quiz->institute?->name ?? 'CSIR-NEIST (Jigyasa)' }}
                            </flux:text>
                        </flux:table.cell>
                        <flux:table.cell class="font-bold text-zinc-900">
                            {{ $quiz->total_questions }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $quiz->status === 'active' ? 'green' : 'yellow' }}" size="sm">
                                {{ ucfirst($quiz->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-500 text-sm">
                            {{ $quiz->created_at->format('M d, Y') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if(auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                            <flux:button href="{{ route('admin.quizzes.view', $quiz->id) }}">
                                View
                            </flux:button>
                            @else
                            <flux:button href="{{ route('organization.quizzes.view', $quiz->id) }}">
                                View
                            </flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>