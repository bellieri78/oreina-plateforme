<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Revue OREINA - Publication scientifique sur les Lépidoptères de France')">
    <title>@yield('title', 'Revue OREINA') - Journal des Lépidoptères de France</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="min-h-screen bg-white">
    @include('partials.journal.header')

    <div class="flex pt-16 sm:pt-20">
        @include('partials.journal.sidebar')

        <main class="flex-1">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
