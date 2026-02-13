<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Dashboard' }}</title>
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
                    <h1 class="text-white text-2xl font-bold">Admin Panel</h1>
                </div> -->
                <div class="flex h-16 items-center gap-4 border-b border-border px-6">
                    <div class="flex items-center justify-center rounded-lg bg-primary-gradient p-2">
                        <flux:icon name="book-open" color="white" />
                    </div>
                    <div>
                        <flux:heading size="3xl" class="font-serif font-extrabold text-primary">JIGYASA</flux:heading>
                    </div>
                </div>

                <div class="py-4 px-6 border-b">
                    <flux:text class="font-bold text-zinc-900">CSIR-NEIST Admin</flux:text>
                    <flux:text>Admin</flux:text>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}"
                        class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <flux:icon variant="mini" name="layout-dashboard" />
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.organizations') }}"
                        class="sidebar-link {{ request()->routeIs('admin.organizations*') ? 'active' : '' }}">
                        <flux:icon variant="mini" name="building-2" />
                        <span>Organizations</span>
                    </a>

                    <a href="{{ route('admin.approvals') }}"
                        class="sidebar-link {{ request()->routeIs('admin.approvals*') ? 'active' : '' }}">
                        <flux:icon variant="mini" name="circle-check-big" />
                        <span>Approvals</span>
                    </a>

                    <a href="{{ route('admin.quizzes') }}"
                        class="sidebar-link {{ request()->routeIs('admin.all-quizzes*') ? 'active' : '' }}">
                        <flux:icon variant="mini" name="file-text" />
                        <span>All Quizzes</span>
                    </a>

                    <a href="{{ route('admin.quizzes.create') }}"
                        class="sidebar-link {{ request()->routeIs('admin.quizzes.create*') ? 'active' : '' }}">
                        <flux:icon variant="mini" name="circle-plus" />
                        <span>Create Quiz</span>
                    </a>

                    <a href="{{ route('admin.all-results') }}"
                        class="sidebar-link {{ request()->routeIs('admin.all-results*') ? 'active' : '' }}">
                        <flux:icon variant="mini" name="trophy" />
                        <span>All Results</span>
                    </a>
                </nav>

                <div class="border-t px-6 py-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button icon="log-out" type="submit" variant="filled" class="w-full border-0 text-red-500">
                            Logout
                        </flux:button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden bg-zinc-100">
            <!-- Page Content -->
            <main class="container mx-auto flex-1 overflow-y-auto p-6 ">
                <header class="mb-6">
                    <flux:heading size="3xl" class="mb-2 text-primary font-bold font-serif">{{ $title }}</flux:heading>
                    <flux:subheading>Welcome back! Here's an overview of the JIGYASA platform.
                    </flux:subheading>
                </header>
                {{ $slot }}
            </main>
        </div>
    </div>
    @fluxScripts
    @stack('scripts')
</body>

</html>