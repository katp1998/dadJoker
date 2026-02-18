
<!DOCTYPE html>
<html lang="en" data-theme="lofi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - DadJoker' : 'DadJoker' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex flex-col bg-base-200 font-sans">
    <nav class="navbar bg-base-100 shadow-sm">
        <div class="navbar-start">
            <a href="{{ auth()->check() ? route('home') : route('welcome') }}" class="btn btn-ghost text-xl">
                🤣 DadJoker
            </a>
        </div>
        <div class="navbar-center hidden md:flex gap-1">
            @auth
                <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">Home</a>
                <a href="{{ route('jokes.create') }}" class="btn btn-ghost btn-sm">Create</a>
                <a href="{{ route('jokes.search') }}" class="btn btn-ghost btn-sm">Search</a>
            @endauth
        </div>
        <div class="navbar-end gap-2">
            @auth
                <span class="text-sm hidden sm:inline opacity-70">{{ auth()->user()->name }}</span>
                <form method='POST' action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-sm">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Sign In</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up</a>
            @endauth
        </div>
    </nav>

    @if (session('success'))
        <div class="toast toast-top toast-center z-50">
            <div class="alert alert-success animate-fade-out">
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <main class="flex-1 container mx-auto px-4 py-8">
        {{ $slot }}
    </main>

    <footer class="footer footer-center p-5 bg-base-300 text-base-content text-xs">
        <div>
            <p>DadJoker — Powered by <a href="https://icanhazdadjoke.com/" class="underline" target="_blank">icanhazdadjoke</a> · Developed by Kat with 💕</p>
        </div>
    </footer>
</body>

</html>