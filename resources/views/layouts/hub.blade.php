<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'OREINA - Association pour l\'étude et la protection des Lépidoptères de France')">
    <title>@yield('title', 'OREINA') - Les Lépidoptères de France</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-white text-oreina-dark">
    @include('partials.hub.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.hub.footer')

    @stack('scripts')
</body>
</html>
