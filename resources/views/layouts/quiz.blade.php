<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Quiz' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-zinc-50">
    <div class="min-h-screen flex flex-col">
        <!-- Quiz Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $quizTitle ?? 'Quiz' }}</h1>
                        <p class="text-sm text-gray-600">{{ $quizSubtitle ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        {{ $headerActions ?? '' }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Quiz Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    @fluxScripts
</body>

</html>