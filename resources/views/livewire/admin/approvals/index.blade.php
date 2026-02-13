<div>
    {{-- Do what you can, with what you have, where you are. - Theodore Roosevelt --}}
    @if ($totalPendings != 0)
        <flex:card class="flex items-center py-4 px-6 rounded-lg gap-4 bg-orange-500/10 border border-orange-400 mb-6">
            <flux:icon name="clock" class="text-orange-500 w-12 h-12" />
            <div class="flex flex-col">
                <flux:heading size="xl" class="text-zinc-900 font-bold">
                    {{ $totalPendings }}
                </flux:heading>
                <flux:subheading>Pending Approval Requests</flux:subheading>
            </div>
        </flex:card>
    @endif

    <flux:card>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <flux:icon name="file-text" class="text-primary" />
                <flux:heading size="xl">Pending Requests</flux:heading>
            </div>
        </div>
        <div class="space-y-2">
            @foreach ($institutes as $institute)
                <flux:card class="flex items-center justify-between border p-2 px-4">
                    <div>
                        <flux:heading size="lg" class="mb-1 capitalize">{{ $institute->name }}</flux:heading>
                        <flux:text size="base">{{ $institute->type }} • {{ $institute->user?->email }}
                        </flux:text>
                        <flux:text class="capitalize">Contact: {{ $institute->contact_person }}</flux:text>
                        <flux:text>Registered: {{ $institute->created_at }}</flux:text>
                    </div>
                    <div class="flex gap-2">
                        <flux:button variant="primary" icon="check" class="bg-green-600 text-white hover:bg-green-700/70">
                            Approve
                        </flux:button>
                        <flux:button variant="primary" icon="x-mark" color="zinc">Reject</flux:button>
                    </div>
                </flux:card>
            @endforeach
            @if ($totalPendings === 0)
                <flux:card class="flex items-center justify-between border p-2 px-4">
                    <flex:text>No Pending Requests!</flex:text>
                </flux:card>
            @endif
        </div>
    </flux:card>
</div>