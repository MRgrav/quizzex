<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4">
    <div class="w-full max-w-md">
        <flux:card class="p-8">
            <div class="text-center mb-8">
                <flux:heading size="xl" class="mb-2">Forgot Password</flux:heading>
                <flux:subheading>Enter your email to receive a password reset link</flux:subheading>
            </div>

            @if ($status)
                <flux:card class="mb-6 bg-green-50 border border-green-200">
                    <div class="flex items-center gap-2 text-green-700">
                        <flux:icon name="check-circle" class="w-5 h-5" />
                        <span>{{ $status }}</span>
                    </div>
                </flux:card>
            @endif

            <form wire:submit="sendResetLink">
                <div class="space-y-6">
                    <flux:field>
                        <flux:label>Email Address</flux:label>
                        <flux:input wire:model="email" type="email" placeholder="your@email.com" required />
                        <flux:error name="email" />
                    </flux:field>

                    <flux:button type="submit" variant="primary" class="w-full">
                        Send Reset Link
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