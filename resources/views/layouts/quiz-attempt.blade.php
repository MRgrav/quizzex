<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Quiz Attempt' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-white">
    <!-- Prevent accidental navigation -->
    <script>
        window.addEventListener('beforeunload', function (e) {
            e.preventDefault();
            e.returnValue = '';
            return 'Are you sure you want to leave? Your quiz progress may be lost.';
        });
    </script>

    <div class="min-h-screen flex flex-col">
        <!-- Minimal header with timer -->
        <header class="bg-white border-b border-zinc-200 sticky top-0 z-50 shadow-sm">
            <div class="max-w-5xl mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ $quizTitle ?? 'Quiz' }}</flux:heading>
                        <flux:text class="text-sm text-zinc-600">{{ $progress ?? '' }}</flux:text>
                    </div>
                    <div class="flex items-center gap-4">
                        {{ $timer ?? '' }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Main content area -->
        <main class="flex-1 bg-zinc-50">
            <div class="max-w-5xl mx-auto px-6 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @fluxScripts
</body>

</html>