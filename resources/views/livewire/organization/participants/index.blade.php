<div>
    <x-slot:header>Participants</x-slot:header>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Add Participant Button -->
    <div class="mb-6">
        <flux:button wire:click="toggleForm" :icon="$showForm ? 'x-mark' : 'plus'" variant="primary">
            {{ $showForm ? 'Cancel' : 'Add Participant' }}
        </flux:button>
    </div>

    <!-- Inline Add Participant Form -->
    @if ($showForm)
        <flux:card class="mb-6">
            <form wire:submit.prevent="addParticipant" class="space-y-4">
                <flux:heading size="lg">Add New Participant</flux:heading>

                <div>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model="name" placeholder="Enter participant name" />
                    @error('name')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <flux:label>Email</flux:label>
                    <flux:input type="email" wire:model="email" placeholder="Enter email address" />
                    @error('email')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <flux:label>Password</flux:label>
                    <flux:input type="password" wire:model="password" placeholder="Enter password (min 8 characters)" />
                    @error('password')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <flux:button type="submit" variant="primary">Add Participant</flux:button>
                    <flux:button type="button" wire:click="toggleForm" variant="ghost">Cancel</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    <!-- Total Participants Card -->
    <flux:card class="flex items-center gap-6 mb-6 p-6 max-w-[400px]">
        <div class="rounded-xl bg-blue-500/10 p-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
        </div>
        <div class="flex flex-col gap-1">
            <div class="text-2xl font-bold">{{ $totalParticipants }}</div>
            <div class="text-zinc-600">Total Participants</div>
        </div>
    </flux:card>

    <!-- Participants Table -->
    <flux:card>
        <flux:table>
            <flux:table.rows>
                @forelse ($participants as $participant)
                    <flux:table.row :key="$participant->id">
                        <flux:table.cell>
                            <div class="font-semibold">{{ $participant->name }}</div>
                            <div class="text-sm text-zinc-600">{{ $participant->email }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-sm">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            {{ $participant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-zinc-100 text-zinc-800' }}">
                                    {{ ucfirst($participant->status) }}
                                </span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-sm text-zinc-600">
                                {{ $participant->created_at->format('M d, Y') }}
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell class="text-center py-8 text-zinc-500" colspan="3">
                            No participants found. Add your first participant!
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <!-- Pagination -->
        <div class="p-4 border-t">
            {{ $participants->links() }}
        </div>
    </flux:card>
</div>