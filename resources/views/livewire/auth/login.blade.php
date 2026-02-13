<div>
    <flux:card>
        <form wire:submit="login" class="space-y-6">
            <div>
                <flux:heading size="lg">Log in to your account</flux:heading>
            </div>

            <div class="space-y-6">
                <flux:input wire:model="email" label="Email" type="email" placeholder="you@example.com" />

                <flux:field>
                    <div class="mb-3 flex justify-between">
                        <flux:label>Password</flux:label>
                        <flux:link href="#" variant="subtle" class="text-sm">Forgot password?</flux:link>
                    </div>

                    <flux:input wire:model="password" type="password" placeholder="••••••••" />

                    <flux:error name="password" />
                </flux:field>

                <flux:checkbox wire:model="remember" label="Remember me" />
            </div>

            <div class="space-y-2">
                <flux:button type="submit" variant="primary" class="w-full bg-primary bg-primary/70 text-white">Sign In
                </flux:button>
                <flux:button href="{{ route('auth.register') }}" variant="ghost" class="w-full">Sign up for an institute
                    account</flux:button>
            </div>
        </form>
    </flux:card>
</div>