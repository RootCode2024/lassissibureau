<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans bg-gray-50">

    <div class="min-h-screen flex flex-col">

        <!-- Header -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center gap-4">
                <!-- Logo réel -->
                <img src="{{ asset('images/logo.png') }}" alt="Logo {{ config('app.name') }}" class="h-24 w-auto">

                <div>
                    <h1 class="text-lg font-semibold text-gray-900">{{ config('app.name') }}</h1>
                    <p class="text-xs text-gray-500">Système de gestion interne</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full">

                @guest
                <!-- Login Card -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8">

                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Connexion</h2>
                        <p class="text-sm text-gray-600">Accédez à votre espace de gestion</p>
                    </div>

                    <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Se connecter
                    </a>

                    <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                        <p class="text-xs text-gray-500">
                            Accès réservé au personnel autorisé
                        </p>
                    </div>

                </div>
                @else
                <!-- Already logged in -->
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-8">

                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Vous êtes connecté</h2>
                        <p class="text-sm text-gray-600">{{ auth()->user()->name }}</p>
                    </div>

                    <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 hover:bg-gray-800 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Accéder au tableau de bord
                    </a>

                </div>
                @endguest

                <!-- System Info -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        Version {{ config('app.version', '1.0.0') }}
                    </p>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="py-6 px-4 sm:px-6 lg:px-8 bg-white border-t border-gray-200">
            <div class="max-w-4xl mx-auto text-center space-y-2">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
                </p>
                <p class="text-xs text-gray-400">
                    Conçu et développé par
                    <a
                        href="https://chrislain-portfolio.vercel.app"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-medium text-gray-600 hover:text-gray-900 transition-colors"
                    >
                        Chrislain Avocegan
                    </a>
                </p>
            </div>
        </footer>

    </div>
</body>
</html>
