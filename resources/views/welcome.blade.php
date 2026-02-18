<x-layout>
    <x-slot:title>Welcome to DadJoker</x-slot:title>

    <div class="max-w-2xl mx-auto">

        <div class="text-center mt-8 mb-10">
            <p class="text-lg">😂🤣🤪😁</p>
            <h1 class="text-5xl font-bold mb-2">DadJoker</h1>
            <p class="text-base-content/60 text-lg">The finest collection of groan-worthy jokes to terrorize people.</p>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body text-center">
                <h2 class="card-title justify-center text-xl mb-4">Random Joke Generator</h2>

                <div id="joke-loader" class="flex justify-center py-6">
                    <span class="loading loading-dots loading-lg text-primary"></span>
                </div>

                <div id="joke-display" class="hidden">
                    <p id="joke-text" class="text-lg font-medium leading-relaxed px-4"></p>
                </div>

                <div id="save-message" class="hidden mt-3">
                    <div class="alert alert-success py-2 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span id="save-message-text"></span>
                    </div>
                </div>

                <div class="card-actions justify-center gap-3 mt-6">
                    <button id="new-joke-btn" class="btn btn-primary">
                    <span class="material-symbols-outlined" style="font-size: 18px;">autorenew</span>
                        New Joke
                    </button>

                    @auth
                        <button id="save-joke-btn" class="btn btn-success">
                            <span class="material-symbols-outlined" style="font-size: 18px;">bookmark</span>
                            Save
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-success" id="save-after-login">
                            <span class="material-symbols-outlined" style="font-size: 18px;">bookmark</span>
                            Save
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        @auth
            <div class="mt-6 flex gap-3 justify-center">
                <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">View My Jokes</a>
                <a href="{{ route('jokes.search') }}" class="btn btn-ghost btn-sm">Search Jokes</a>
            </div>
        @endauth
    </div>

    <script>
        const SAVE_URL  = '{{ route("jokes.save-from-api") }}';
        const CSRF      = '{{ csrf_token() }}';
        const IS_AUTH   = {{ auth()->check() ? 'true' : 'false' }};

        let currentJoke = null;

        const loader    = document.getElementById('joke-loader');
        const display   = document.getElementById('joke-display');
        const jokeText  = document.getElementById('joke-text');
        const newBtn    = document.getElementById('new-joke-btn');
        const saveBtn   = document.getElementById('save-joke-btn');
        const saveMsg   = document.getElementById('save-message');
        const saveMsgTxt = document.getElementById('save-message-text');

        async function fetchRandomJoke() {
            loader.classList.remove('hidden');
            display.classList.add('hidden');
            saveMsg.classList.add('hidden');

            try {
                const res = await fetch('https://icanhazdadjoke.com/', {
                    headers: {
                        'Accept': 'application/json',
                        'User-Agent': 'DadJoker App (https://github.com/katp1998)',
                    },
                });
                const data = await res.json();
                currentJoke = data;
                jokeText.textContent = data.joke;
            } catch {
                jokeText.textContent = 'Could not fetch a joke right now. Try again!';
                currentJoke = null;
            } finally {
                loader.classList.add('hidden');
                display.classList.remove('hidden');
            }
        }

        newBtn.addEventListener('click', fetchRandomJoke);

        if (saveBtn && IS_AUTH) {
            saveBtn.addEventListener('click', async () => {
                if (!currentJoke) return;
                saveBtn.disabled = true;

                try {
                    const res = await fetch(SAVE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            joke:   currentJoke.joke,
                            api_id: currentJoke.id,
                        }),
                    });
                    const data = await res.json();
                    saveMsgTxt.textContent = data.message;
                    saveMsg.classList.remove('hidden');
                } catch {
                    saveMsgTxt.textContent = 'Failed to save. Please try again.';
                    saveMsg.classList.remove('hidden');
                } finally {
                    saveBtn.disabled = false;
                }
            });
        }

        if(saveGuestBtn && !IS_AUTH) {
            saveGuestBtn.addEventListener('click', (e) => {
                if(currentJoke){
                    sessionStorage.setItem('pendingJoke', JSON.stringify({
                        joke: currentJoke.joke,
                        api_id: currentJoke.id
                    }))
                }
            })
        }

        fetchRandomJoke();
    </script>
</x-layout>