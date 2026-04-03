<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'OREINA - Association pour l\'étude et la protection des Lépidoptères de France')">
    <title>@yield('title', 'OREINA') - Les Lépidoptères de France</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen flex flex-col" style="background: var(--bg); color: var(--text);">
    <script>lucide.createIcons?.();</script>
    @include('partials.hub.header')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.hub.footer')

    @stack('scripts')
</body>
</html>
