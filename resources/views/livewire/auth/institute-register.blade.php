<div>
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <flux:card>
        <form wire:submit="register" class="space-y-6">
            <flux:heading size="lg">Create an institute account</flux:heading>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <flux:input wire:model="form.name" label="Full Name" placeholder="Your name" />

                    <flux:input wire:model="form.email" label="Email" type="email" placeholder="you@example.com" />
                    <flux:input wire:model="form.password" label="Password" type="password" placeholder="••••••••" />
                    <flux:input wire:model="form.password_confirmation" label="Confirm Password" type="password"
                        placeholder="••••••••" />
                </div>

                <flux:separator />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="form.institute_name" label="Institute Name"
                        placeholder="Name of your institute" />

                    <flux:select wire:model="form.type" label="Institute Type" placeholder="Select type...">
                        <flux:select.option value="school">School</flux:select.option>
                        <flux:select.option value="college">College</flux:select.option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="form.address" label="Address" rows="2"
                    placeholder="Full address of the institute" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="form.contact_person" label="Contact Person"
                        placeholder="Name of contact person" />
                    <flux:input wire:model="form.phone" label="Phone" placeholder="Contact number" />
                </div>
            </div>

            <div class="space-y-2">
                <flux:button type="submit" variant="primary" class="w-full bg-primary hover:bg-primary/70 text-white">
                    Register Institute
                </flux:button>
                <flux:button href="{{ route('auth.login') }}" variant="ghost" class="w-full">Already have an account?
                    Log in
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>