<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4">
    <div class="w-full max-w-md">
        <flux:card class="p-8">
            <div class="text-center mb-8">
                <flux:heading size="xl" class="mb-2">Reset Password</flux:heading>
                <flux:subheading>Enter your new password</flux:subheading>
            </div>

            <form wire:submit="resetPassword">
                <div class="space-y-6">
                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="your@email.com" required />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:field>
                        <flux:label>New Password</flux:label>
                        <flux:input wire:model="password" type="password" placeholder="••••••••" required />
                        <flux:error name="password" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Confirm Password</flux:label>
                        <flux:input wire:model="password_confirmation" type="password" placeholder="••••••••"
                            required />
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full">
                        Reset Password
                    </flux:button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('auth.login') }}" class="text-sm text-zinc-600 hover:text-zinc-900">
                    Back to Login
                </a>
            </div>
        </flux:card>
    </div>
</div>