<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ isset($title) ? $title.' - DadJoker' : 'DadJoker' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
        <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body>
        <nav>
            <div class="navbar-start">
                <a href="{{ auth()->check() ? route('home') : route('welcome') }}"></a>
            </div>
        </nav>
        <main></main>
        <footer></footer>
    </body>
</html>