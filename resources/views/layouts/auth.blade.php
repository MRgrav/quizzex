<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-blue-200/60">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo/Header -->
            <div class="text-center mb-8">
                <div class="inline-block bg-primary-gradient p-4 rounded-2xl mb-4">
                    <flux:icon name="book-open" class="text-white h-10 w-10" size="lg" />
                </div>
                <flux:heading size="3xl" class="text-white">Quiz Platform</flux:heading>
                <flux:subheading size="lg" class="text-zinc-100">{{ $subtitle ?? 'Welcome back' }}</flux:subheading>
            </div>

            <!-- Card -->
            {{ $slot }}

            <!-- Footer -->
            <div class="text-center mt-6 text-sm text-gray-600">
                {{ $footer ?? '' }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>