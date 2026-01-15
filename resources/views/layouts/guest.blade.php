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
<body class="font-sans text-gray-900 antialiased">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    <!-- LEFT : Form -->
    <div class="flex flex-col justify-center items-center px-6 sm:px-10 lg:px-16 bg-white">

        <!-- Logo -->
        <a href="/" class="mb-8">
            <img
                src="{{ asset('images/logo.png') }}"
                alt="Logo"
                class="h-24 w-auto"
            >
        </a>

        <!-- Content -->
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

    <!-- RIGHT : Image -->
    <div class="hidden lg:block relative">
        <img
            src="{{ asset('images/auth-image.jpg') }}"
            alt="Boutique électronique"
            class="absolute inset-0 h-full w-full object-cover"
        >

        <!-- Optional overlay -->
        <div class="absolute inset-0 bg-black/10"></div>
    </div>

</div>

</body>
</html>
