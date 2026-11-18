<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'El Oráculo') }}</title>

        <meta
            name="description"
            content="El Oráculo: tiradas de tarot que se sellan antes de abrirse y se pueden verificar después."
        >

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon-32.png" type="image/png" sizes="32x32">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <meta name="theme-color" content="#150F26">
        <meta name="color-scheme" content="dark light">

        {{-- La corriente guardada se aplica antes del primer pintado. --}}
        <script>
            (function () {
                var theme = null;
                try { theme = window.localStorage.getItem('oraculo.theme'); } catch (e) {}
                @auth
                    theme = @json(auth()->user()->theme) || theme;
                @endauth
                if (theme) { document.documentElement.dataset.theme = theme; }
            })();
        </script>

        {{-- Tipografías de las corrientes visuales (ver resources/js/themes.js). --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600&family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Eczar:wght@400;600&family=IM+Fell+English:ital@0;1&family=IM+Fell+English+SC&family=Marcellus&family=MedievalSharp&family=Spectral:ital,wght@0,400;0,600;1,400&family=UnifrakturMaguntia&display=swap"
            rel="stylesheet"
        >

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
