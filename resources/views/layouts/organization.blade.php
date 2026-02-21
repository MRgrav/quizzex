<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Institute Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance

    {{-- Trix Editor Styles --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <style>
        trix-toolbar .trix-button-group--file-tools {
            display: none;
        }

        .trix-content {
            min-height: 150px;
            border: 1px solid #d4d4d8;
            border-radius: 0.5rem;
        }

        .trix-content:focus-within {
            border-color: #3b82f6;
            ring: 2px;
            ring-color: #3b82f6;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-zinc-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg flex-shrink-0 hidden md:block">
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <!-- <div class="bg-primary-gradient p-6">
                    <h1 class="text-white text-2xl font-bold">Institute</h1>
                </div> -->
                <div class="flex h-16 items-center gap-4 border-b border-border px-6">
                    <div class="flex items-center justify-center rounded-lg bg-primary-gradient p-2">
                        <flux:icon name="book-open" color="white" />
                    </div>
                    <div>
                        <flux:heading size="3xl" class="font-serif font-extrabold text-primary">JIGYASA</flux:heading>
                    </div>
                </div>

                <div class="py-4 px-6 border-b capitalize">
                    <flux:text class="font-bold text-zinc-900">{{ \Auth::user()->institute->name }}</flux:text>
                    <flux:text>{{ \Auth::user()->institute->type }}</flux:text>
                    <!-- <flux:text>{{ \Auth::user()->name }}</flux:text> -->
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <a href="{{ route('organization.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('organization.dashboard') ? 'active' : '' }}">
                        <flux:icon name="layout-dashboard" variant="mini" />
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('organization.participants') }}"
                        class="sidebar-link {{ request()->routeIs('organization.participants*') ? 'active' : '' }}">
                        <flux:icon name="users" variant="mini" />
                        <span>Participants</span>
                    </a>

                    <a href="{{ route('organization.quiz.create') }}"
                        class="sidebar-link {{ request()->routeIs('organization.quiz.create') ? 'active' : '' }}">
                        <flux:icon name="circle-plus" variant="mini" />
                        <span>Create Quiz</span>
                    </a>

                    <a href="{{ route('organization.quizzes') }}"
                        class="sidebar-link {{ request()->routeIs('organization.quizzes*') ? 'active' : '' }}">
                        <flux:icon name="file-text" variant="mini" />
                        <span>My Quizzes</span>
                    </a>

                    <a href="{{ route('organization.results') }}"
                        class="sidebar-link {{ request()->routeIs('organization.results*') ? 'active' : '' }}">
                        <flux:icon name="trophy" variant="mini" />
                        <span>Results</span>
                    </a>
                </nav>

                <div class="border-t px-6 py-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button icon="log-out" type="submit" variant="primary"
                            class="border-red-500 w-full border text-red-500 hover:bg-red-500/10">
                            Logout
                        </flux:button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <header class="flex items-end justify-between mb-6">
                    <div>
                        <flux:heading size="3xl" class="mb-2 text-primary font-bold font-serif">{{ $title }}
                        </flux:heading>
                        <flux:subheading>Welcome back! Here's an overview of the JIGYASA platform.
                        </flux:subheading>
                    </div>
                    <div>
                        @if (request()->routeIs('organization.dashboard'))
                            <flux:button href="{{ route('organization.quiz.create') }}" variant="primary"
                                icon="circle-plus" class="bg-primary-gradient text-white shadow hover:shadow-lg">Add Quiz
                            </flux:button>
                        @endif
                        <!-- @if (request()->routeIs('organization.participants'))
                            <flux:button variant="primary" icon="circle-plus"
                                class="bg-primary-gradient text-white shadow hover:shadow-lg">Add Participant</flux:button>
                        @endif -->
                    </div>
                </header>
                {{ $slot }}
            </main>
        </div>
    </div>
    @fluxScripts
    @stack('scripts')
</body>

</html>