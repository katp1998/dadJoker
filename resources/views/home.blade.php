<x-layout>
    <x-slot:title>My Jokes</x-slot:title>

    <div class="max-w-2xl mx-auto">

        <div class="flex items-center justify-between mt-4 mb-6">
            <h1 class="text-3xl font-bold">My Jokes</h1>
            <div class="flex gap-2">
                <a href="{{ route('jokes.create') }}" class="btn btn-primary btn-sm">
                    <span class="material-symbols-outlined" style="font-size: 16px;">stylus_note</span>
                    Create
                </a>
                <a href="{{ route('jokes.search') }}" class="btn btn-outline btn-sm">
                    <span class="material-symbols-outlined" style="font-size: 16px;">Search</span>
                    Search
                </a>
            </div>
        </div>

        <div class="card bg-base-100 shadow mb-8">
            <div class="card-body">
                <h2 class="card-title text-lg">
                    <span class="material-symbols-outlined" style="font-size: 20px;">casino</span>
                    Random Joke</h2>
                <div id="joke-loader" class="flex justify-center py-4">
                    <span class="loading loading-dots loading-md text-primary"></span>
                </div>

                <div id="joke-display" class="hidden">
                    <p id="joke-text" class="leading-relaxed"></p>
                </div>

                <div id="save-message" class="hidden mt-2">
                    <div class="alert alert-success py-2 text-sm">
                        <span id="save-message-text"></span>
                    </div>
                </div>

                <div class="card-actions justify-end gap-2 mt-4">
                    <button id="new-joke-btn" class="btn btn-ghost btn-sm">
                        <span class="material-symbols-outlined" style="font-size: 18px;">autorenew</span>
                        New
                    </button>
                    <button id="save-joke-btn" class="btn btn-success btn-sm">
                        <span class="material-symbols-outlined" style="font-size: 18px;">bookmark</span>
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Saved Feed -->
        <div class="space-y-4">
            <h2 class="text-xl font-semibold">Saved Jokes</h2>
            @forelse ($jokes as $joke)
                <x-joke :joke="$joke" />
            @empty
                <div class="hero py-12">
                    <div class="hero-content text-center">
                        <div>
                            <p class="text-6xl mb-4">
                                <span class="material-symbols-outlined">sentiment_sad</span>
                            </p>
                            <p class="text-base-content/60">No jokes saved yet. Generate one above or search for more!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        const SAVE_URL   = '{{ route("jokes.save-from-api") }}';
        const CSRF       = '{{ csrf_token() }}';
        let currentJoke  = null;

        const loader     = document.getElementById('joke-loader');
        const display    = document.getElementById('joke-display');
        const jokeText   = document.getElementById('joke-text');
        const newBtn     = document.getElementById('new-joke-btn');
        const saveBtn    = document.getElementById('save-joke-btn');
        const saveMsg    = document.getElementById('save-message');
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
                jokeText.textContent = 'Could not get a joke: no pun intended. Try again!';
                currentJoke = null;
            } finally {
                loader.classList.add('hidden');
                display.classList.remove('hidden');
            }
        }

        newBtn.addEventListener('click', fetchRandomJoke);

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
                if (res.status === 201) {
                    setTimeout(() => window.location.reload(), 800);
                }
            } catch {
                saveMsgTxt.textContent = 'Failed to save the joke. Please try again.';
                saveMsg.classList.remove('hidden');
            } finally {
                saveBtn.disabled = false;
            }
        });

        (async function checkForPendingJoke() {
            const pendingJoke = sessionStorage.getItem('pendingJoke');
            if (pendingJoke){
                try {
                    const jokeData = JSON.parse(pendingJoke);
                    const res = await fetch(SAVE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(jokeData),
                });
                sessionStorage.removeItem('pendingJoke');
                
                if (res.ok) {
                    window.location.reload();
                }

                } catch (error) {
                    console.error('Failed to save pending joke:', error);
                    sessionStorage.removeItem('pendingJoke');
                }
            }
        })

        fetchRandomJoke();
    </script>
</x-layout>