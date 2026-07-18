<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TravelBox ERP') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50 flex flex-col">
        <x-toast />
        <livewire:layout.navigation />

        <div class="flex flex-1 overflow-hidden">
            <main class="flex-1 min-w-0 p-4 lg:p-6 overflow-y-auto">
                @if (isset($header))
                <header class="mb-6">
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $header }}</h2>
                </header>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
    </div>
    <style>[x-cloak]{display:none!important}</style>
    @stack('styles')
    @livewireScripts
    @stack('scripts')
</body>
</html>