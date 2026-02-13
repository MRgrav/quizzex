<div>
    {{-- Simplicity is an acquired taste. - Katharine Gerould --}}

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
    <flux:card class="bg-white shadow-card hover-levitate p-0 overflow-hidden border-none">
        <flux:table class="w-full">
            <flux:table.columns>
                <flux:table.column>Institute Name</flux:table.column>
                <flux:table.column>Admin User</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Joined</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($institutes as $institute)
                    <flux:table.row :key="$institute->id">
                        <flux:table.cell class="font-bold text-zinc-900">{{ $institute->name }}</flux:cell>
                            <flux:table.cell>
                                <div class="text-sm">
                                    <div class="font-medium">{{ $institute->user?->name }}</div>
                                    <div class="text-zinc-500 text-xs">{{ $institute->user?->email }}</div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm">{{ ucfirst($institute->type) }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $institute->status === 'active' ? 'green' : 'yellow' }}" size="sm">
                                    {{ ucfirst($institute->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-zinc-500 text-sm">
                                {{ $institute->created_at->format('M d, Y') }}
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($institute->status === \App\Models\Institute::STATUS_PENDING)
                                    <flux:button.group>
                                        <flux:button wire:click="approve({{ $institute->id }})"
                                            wire:confirm="Are you sure you want to approve {{ $institute->name }}?"
                                            variant="primary" class="bg-green-600 hover:bg-green-700 text-white" icon="check">
                                            <span wire:loading.remove target="approve({{ $institute->id }})">Approve</span>
                                            <span wire:loading target="approve({{ $institute->id }})">Saving...</span>
                                        </flux:button>
                                        <flux:button wire:click="reject({{ $institute->id }})"
                                            wire:confirm="Are you sure you want to reject {{ $institute->name }}?"
                                            variant="danger" icon="x-mark"><span wire:loading.remove
                                                target="approve({{ $institute->id }})">Reject</span>
                                            <span wire:loading target="approve({{ $institute->id }})">Saving...</span>
                                        </flux:button>
                                    </flux:button.group>
                                @else
                                    <span class="text-green-600 text-sm font-medium flex items-center gap-1">
                                        <flux:icon.check-circle class="size-4" /> Approved
                                    </span>
                                @endif
                            </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="p-4 border-t border-zinc-100">
            {{ $institutes->links() }}
        </div>
    </flux:card>
</div>