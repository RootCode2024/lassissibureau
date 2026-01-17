<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen">
            <!-- Sidebar -->
            <livewire:layout.sidebar />

            <!-- Main Content -->
            <div class="lg:pl-64">
                <!-- Top Navigation -->
                <livewire:layout.topbar />

                <!-- Page Content -->
                <main class="py-6">
                    <div class="w-[90%] mx-auto">
                        <!-- Page Header avec actions -->
                        @if (isset($header))
                            <div class="px-4 sm:px-6 lg:px-8 mb-8">
                                <div class="flex items-center justify-between gap-4">
                                    <!-- Left side: Header content -->
                                    <div class="flex-1 min-w-0">
                                        {{ $header }}
                                    </div>

                                    <!-- Right side: Actions (si définies) -->
                                    @if (isset($actions))
                                        <div class="flex-shrink-0">
                                            {{ $actions }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Main Content -->
                        <div class="px-4 sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
