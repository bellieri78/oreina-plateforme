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
<style>
    body {
        font-family: 'Inter', sans-serif;
        color: var(--text, #1C2B27);
        background: var(--bg, #F4F1ED);
        margin: 0;
        padding: 0;
        line-height: 1.5;
    }
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at 0% 0%, rgba(53,107,138,0.04), transparent 18%),
            radial-gradient(circle at 100% 0%, rgba(133,183,157,0.05), transparent 18%),
            radial-gradient(circle at 100% 100%, rgba(237,196,66,0.05), transparent 20%);
        z-index: -1;
    }
    a { color: inherit; text-decoration: none; }
    img { max-width: 100%; display: block; }
    .container {
        width: min(calc(100% - 32px), 1180px);
        margin: 0 auto;
    }
    .icon {
        width: 18px; height: 18px;
        display: inline-flex; align-items: center; justify-content: center;
        flex: 0 0 18px;
    }
    .icon svg { width: 18px; height: 18px; stroke-width: 2; }
    .icon-white { color: white; }
    .icon-blue { color: var(--blue, #356B8A); }
    .icon-sage { color: var(--forest, #16302B); }
    .icon-gold { color: #8b6c05; }
    .icon-coral { color: var(--coral, #EF7A5C); }
    h1, h2, h3, h4 { font-weight: 800; line-height: 1.05; letter-spacing: -0.04em; }
</style>
<body>
    @include('partials.hub.header')

    <main>
        @yield('content')
    </main>

    @include('partials.hub.footer')

    <script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
    @stack('scripts')
</body>
</html>
