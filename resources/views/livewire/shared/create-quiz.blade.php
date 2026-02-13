<div>
    <flux:card>
        <form wire:submit="create" class="space-y-6">
            <flux:heading size="lg">Create Quiz</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="form.title" label="Quiz Title" />
                <flux:input type="number" wire:model="form.duration_minutes" label="Duration (Minutes)"
                    placeholder="Time limit per attempt" />
            </div>

            <flux:textarea wire:model="form.description" rows="2" label="Description" />

            {{-- Scheduling Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input type="datetime-local" wire:model="form.start_time" label="Start Time (Optional)"
                    placeholder="When quiz becomes available" />
                <flux:input type="datetime-local" wire:model="form.end_time" label="End Time (Optional)"
                    placeholder="When quiz closes" />
            </div>

            @if(auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                <flux:select wire:model="form.institute_id" label="Assign Institute (Optional)"
                    placeholder="Select Institute">
                    <flux:select.option value="">Global / CSIR</flux:select.option>
                    @foreach(\App\Models\Institute::all() as $inst)
                        <flux:select.option value="{{ $inst->id }}">{{ $inst->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif

            <flux:select wire:model="form.status" label="Status">
                @foreach(\App\Models\Quiz::STATUSES as $status)
                    <flux:select.option value="{{ $status }}">{{ ucfirst($status) }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:button type="submit" variant="primary" class="bg-primary text-white hover:bg-blue-700 w-full">
                Create Quiz
            </flux:button>
        </form>
    </flux:card>
</div>