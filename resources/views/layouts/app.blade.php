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
        <script src="//unpkg.com/alpinejs" defer></script>


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-screen">
        <div class="min-h-screen dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-gradient-to-br from-primary-dark to-primary-soft shadow-lg border-b-4 border-primary">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <h1 class="text-3xl font-bold text-accent drop-shadow">
                        {{ $header }}
                    </h1>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <div class="shadow-2xl p-4 md:p-12">
                {{ $slot }}
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
