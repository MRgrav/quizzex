<div>
    {{-- Simplicity is an acquired taste. - Katharine Gerould --}}
    <flux:card>
        <div class="flex justify-between items-center mb-4">
            <flux:heading class="flex gap-2 items-center" size="xl">
                <flux:icon name="building-2" class="text-primary" />Institute's Details
            </flux:heading>
            <flux:button wire:click="back" variant="primary">Back</flux:button>
        </div>
        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8 pb-8 border-b border-zinc-100">
                <div>
                    <flux:label>Name</flux:label>
                    <flux:text>{{ $institute->name }}</flux:text>
                </div>
                <div>
                    <flux:label>Email</flux:label>
                    <flux:text>{{ $institute->email }}</flux:text>
                </div>
                <div>
                    <flux:label>Phone</flux:label>
                    <flux:text>{{ $institute->phone }}</flux:text>
                </div>
                <div>
                    <flux:label>Address</flux:label>
                    <flux:text>{{ $institute->address }}</flux:text>
                </div>
                <div>
                    <flux:label>City</flux:label>
                    <flux:text>{{ $institute->city }}</flux:text>
                </div>
                <div>
                    <flux:label>State</flux:label>
                    <flux:text>{{ $institute->state }}</flux:text>
                </div>
                <div>
                    <flux:label>Country</flux:label>
                    <flux:text>{{ $institute->country }}</flux:text>
                </div>
                <div>
                    <flux:label>Pincode</flux:label>
                    <flux:text>{{ $institute->pincode }}</flux:text>
                </div>
                <div>
                    <flux:label>Status</flux:label>
                    <flux:text>{{ $institute->status }}</flux:text>
                </div>
                <div>
                    <flux:label>Type</flux:label>
                    <flux:text>{{ $institute->type }}</flux:text>
                </div>
                <div>
                    <flux:label>Created At</flux:label>
                    <flux:text>{{ $institute->created_at }}</flux:text>
                </div>
                <div>
                    <flux:label>Updated At</flux:label>
                    <flux:text>{{ $institute->updated_at }}</flux:text>
                </div>
            </div>
            <div class="flex gap-4 items-center">
                @if ($institute->status === \App\Models\Institute::STATUS_PENDING)
                    <flux:button wire:click="approve({{ $institute->id }})"
                        wire:confirm="Are you sure you want to approve {{ $institute->name }}?" variant="primary"
                        class="bg-green-600 hover:bg-green-700 text-white" icon="check">
                        <span wire:loading.remove target="approve({{ $institute->id }})">Approve</span>
                        <span wire:loading target="approve({{ $institute->id }})">Saving...</span>
                    </flux:button>
                    <flux:button wire:click="reject({{ $institute->id }})"
                        wire:confirm="Are you sure you want to reject {{ $institute->name }}?" variant="danger"
                        icon="x-mark"><span wire:loading.remove target="approve({{ $institute->id }})">Reject</span>
                        <span wire:loading target="approve({{ $institute->id }})">Saving...</span>
                    </flux:button>
                @else
                    <span class="text-zinc-600 text-sm font-medium flex items-center gap-1">
                        <flux:icon.check-circle class="size-4" /> Disabled
                    </span>
                @endif
            </div>
        </div>
    </flux:card>

</div>