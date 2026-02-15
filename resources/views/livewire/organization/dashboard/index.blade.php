<div>
    <!-- <x-⚡page-header title="Dashboard" subtext="Welcome to your dashboard" /> -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <flux:card class="p-10 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="lg" class="mb-1">Total Participants</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">
                        {{ $stats['total_participants_global'] }}
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
                    <flux:text size="lg" class="mb-1">Quizzes Created</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">
                        {{ $stats['total_quizzes'] }}
                    </flux:text>
                </div>
                <div class="rounded-xl p-3 bg-green-600/10">
                    <flux:icon name="file-text" class="text-green-600" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-10 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="lg" class="mb-1">Active Quizzes</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">
                        {{ $stats['active_quizzes'] }}
                    </flux:text>
                </div>
                <div class="rounded-xl p-3 bg-orange-500/10">
                    <flux:icon name="trending-up" class="text-orange-500" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-10 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <flux:text size="lg" class="mb-1">Total Quizzes</flux:text>
                    <flux:text size="3xl" class="font-bold text-zinc-900">{{ $stats['total_quizzes_global'] }}
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
                @foreach ($recentQuizzes as $quiz)
                    <flux:card class="flex items-center justify-between border p-2 px-4">
                        <div>
                            <flux:heading size="lg" class="mb-1 capitalize">{{ $quiz->title }}</flux:heading>
                            <flux:subheading size="base" class="mb-1">{{ $quiz->institute->name ?? 'CSIR-NEIST (Jigyasa)' }}
                                • {{ $quiz->total_questions }} questions</flux:subheading>
                        </div>
                        @if ($quiz->isLive())
                            <flux:badge rounded color="green" class="bg-green-600/10">Live</flux:badge>
                        @elseif ($quiz->status === 'active')
                            <flux:badge rounded color="blue" class="bg-blue-600/10">Active</flux:badge>
                        @elseif ($quiz->status === 'inactive')
                            <flux:badge rounded color="red" class="bg-red-600/10">Inactive</flux:badge>
                        @else
                            <flux:badge rounded color="gray" class="bg-gray-600/10">Draft</flux:badge>
                        @endif
                    </flux:card>
                @endforeach
            </div>
        </flux:card>
    </div>
</div>