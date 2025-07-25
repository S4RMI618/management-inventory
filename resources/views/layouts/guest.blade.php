<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-primary-dark via-primary-soft to-primary-bg min-h-screen flex items-center justify-center">
        <div class="w-full max-w-md mx-auto">
            <div class="flex flex-col items-center mb-8">
                <h1 class="mt-4 text-3xl font-bold text-white tracking-widest drop-shadow">Bienvenido</h1>
            </div>
            <div class="bg-gradient-to-br from-primary-dark to-primary-soft border-4 border-primary shadow-2xl rounded-2xl px-8 py-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
