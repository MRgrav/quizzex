<div>
    <!-- <x-⚡page-header title="Dashboard" subtext="Welcome to your dashboard" /> -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <flux:card class="p-10 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="lg" class="mb-1">Total Organizations</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">{{ \App\Models\Institute::count() }}
                    </flux:text>
                </div>
                <div class="rounded-xl p-3 bg-primary/10">
                    <flux:icon name="building-2" class="text-primary" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-10 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="lg" class="mb-1">Pending Approvals</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">
                        {{ \App\Models\Institute::where('status', 'pending')->count() }}
                    </flux:text>
                </div>
                <div class="rounded-xl p-3 bg-orange-500/10">
                    <flux:icon name="clock" class="text-orange-500" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-10 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="lg" class="mb-1">Total Participants</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">
                        {{ \App\Models\User::where('role', 'participant')->count() }}
                    </flux:text>
                </div>
                <div class="rounded-xl p-3 bg-green-500/10">
                    <flux:icon name="users" class="text-green-500" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-10 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="lg" class="mb-1">Total Quizzes</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">{{ \App\Models\Quiz::count() }}
                    </flux:text>
                </div>
                <div class="rounded-xl p-3 bg-blue-500/10">
                    <flux:icon name="file-text" class="text-primary" />
                </div>
            </div>
        </flux:card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <flux:icon name="users" class="text-primary" />
                    <flux:heading size="xl">Participants</flux:heading>
                </div>
                <div>
                    <a href="/admin/institutes" class="flex items-center gap-2">
                        <flux:text size="base" class="font-bold text-zinc-900">View All</flux:text>
                        <flux:icon name="arrow-right" />
                    </a>
                </div>
            </div>
            <div class="space-y-2">
                @foreach ($participants as $participant)
                    <flux:card class="flex items-center justify-between border p-2 px-4">
                        <div>
                            <flux:heading size="lg" class="mb-1 capitalize">{{ $participant->name }}</flux:heading>
                            <flux:subheading size="base" class="mb-1">{{ $participant->email }}</flux:subheading>
                        </div>
                        <div class="flex gap-2">
                            <flux:button variant="primary" color="emerald">Approve
                            </flux:button>
                            <flux:button variant="primary" color="zinc">Reject</flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </flux:card>
        <flux:card>
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <flux:icon name="file-text" class="text-blue-500" />
                    <flux:heading size="xl">Recent Quizzes</flux:heading>
                </div>
                <div>
                    <a href="/admin/institutes" class="flex items-center gap-2">
                        <flux:text size="base" class="font-bold text-zinc-900">View All</flux:text>
                        <flux:icon name="arrow-right" />
                    </a>
                </div>
            </div>
            <div class="space-y-2">
                <flux:card class="flex items-center justify-between border p-2 px-4">
                    <div>
                        <flux:heading size="lg" class="mb-1">Algebra Fundamentals</flux:heading>
                        <flux:subheading size="base" class="mb-1">CSIR-NEIST (Jigyasa) • 20 questions</flux:subheading>
                    </div>
                    <flux:badge rounded color="green" class="bg-green-600/10">New</flux:badge>
                </flux:card>
                <flux:card class="flex items-center justify-between border p-2 px-4">
                    <div>
                        <flux:heading size="lg" class="mb-1">Algebra Fundamentals</flux:heading>
                        <flux:subheading size="base" class="mb-1">CSIR-NEIST (Jigyasa) • 20 questions</flux:subheading>
                    </div>
                    <flux:badge rounded color="blue" class="bg-blue-600/10">Upcoming</flux:badge>
                </flux:card>
                <flux:card class="flex items-center justify-between border p-2 px-4">
                    <div>
                        <flux:heading size="lg" class="mb-1">Algebra Fundamentals</flux:heading>
                        <flux:subheading size="base" class="mb-1">CSIR-NEIST (Jigyasa) • 20 questions</flux:subheading>
                    </div>
                    <flux:badge rounded color="zinc" class="bg-zinc-600/10">Completed</flux:badge>
                </flux:card>
            </div>
        </flux:card>
    </div>
</div>