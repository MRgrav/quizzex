<div>
    {{-- Simplicity is an acquired taste. - Katharine Gerould --}}

    <flux:card class="flex gap-3 items-center bg-white shadow-card p-4 flex-wrap lg:flex-nowrap gap-4 mb-4">
        <div class="grow flex-1 min-w-[300px] w-full md:w-64">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search name..." />
        </div>
    </flux:card>
    <flux:card class="card-hover flex items-center gap-6 mb-6 p-6 max-w-[400px]">
        <div class="rounded-xl bg-blue-500/10 p-4">
            <flux:icon name="users" class="text-primary h-8 w-8" />
        </div>
        <div class="flex flex-col gap-1">
            <flex:text size="3xl" class="text-2xl font-bold">{{ $totalParticipants }}</flex:text>
            <flex:text>Total Participants</flex:text>
        </div>
    </flux:card>
    <flux:card class="bg-white shadow-card hover-levitate p-0 overflow-hidden border-none">
        <flux:table class="w-full">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Class</flux:table.column>
                <flux:table.column>Email</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($participants as $participant)
                    <flux:table.row :key="$participant->id">
                        <flux:table.cell>
                            <flux:text>{{ $participant->name }}</flux:text>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm">
                                {{ $participant->class }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:text>{{ $participant->email }}</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>