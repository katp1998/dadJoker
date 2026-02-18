<x-layout>
    <x-slot:title>Search Jokes</x-slot:title>

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-3 mt-4 mb-6">
            <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">
                <span class="material-symbols-outlined" style="font-size: 20px;">arrow_back</span>
                Back
            </a>
            <h1 class="text-3xl font-bold">
                Search Jokes 
            </h1>
        </div>

        <div class="card bg-base-100 shadow mb-6">
            <div class="card-body">
                <p class="text-base-content/60 text-sm mb-3">
                    Search the icanhazdadjoke.com library...
                </p>
                <div class="flex gap-2">
                    <input
                        id="search-input"
                        type="text"
                        placeholder="e.g. chicken, programmer, school..."
                        class="input input-bordered flex-1"
                        autocomplete="off"
                    />
                    <button id="search-btn" class="btn btn-primary">Search</button>
                </div>
            </div>
        </div>

        <div id="search-status" class="hidden text-sm text-base-content/60 mb-4 flex items-center justify-between">
            <span id="status-text"></span>
            <div class="flex gap-2">
                <button id="prev-btn" class="btn btn-ghost btn-xs" disabled>
                    <span class="material-symbols-outlined" style="font-size: 20px;">arrow_back</span>
                    Prev
                </button>
                <span id="page-indicator" class="self-center text-xs"></span>
                <button id="next-btn" class="btn btn-ghost btn-xs" disabled>
                    Next
                    <span class="material-symbols-outlined" style="font-size: 20px;">arrow_forward</span>
                </button>
            </div>
        </div>

        <div id="search-loader" class="hidden justify-center py-10">
            <span class="loading loading-dots loading-lg text-primary"></span>
        </div>

        <div id="results-container" class="space-y-3"></div>

        <div id="toast" class="toast toast-top toast-center z-50 hidden">
            <div class="alert alert-success py-2">
                <span id="toast-text" class="text-sm"></span>
            </div>
        </div>
    </div>

    <script>
        const API_BASE  = 'https://icanhazdadjoke.com';
        const SAVE_URL  = '{{ route("jokes.save-from-api") }}';
        const CSRF      = '{{ csrf_token() }}';

        const searchInput  = document.getElementById('search-input');
        const searchBtn    = document.getElementById('search-btn');
        const loader       = document.getElementById('search-loader');
        const statusBar    = document.getElementById('search-status');
        const statusText   = document.getElementById('status-text');
        const container    = document.getElementById('results-container');
        const prevBtn      = document.getElementById('prev-btn');
        const nextBtn      = document.getElementById('next-btn');
        const pageIndicator = document.getElementById('page-indicator');
        const toast        = document.getElementById('toast');
        const toastText    = document.getElementById('toast-text');

        let currentTerm    = '';
        let currentPage    = 1;
        let totalPages     = 1;

        function showToast(msg, isError = false) {
            toastText.textContent = msg;
            const alert = toast.querySelector('.alert');
            alert.classList.toggle('alert-error', isError);
            alert.classList.toggle('alert-success', !isError);
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }

        function buildCard(joke) {
            const div = document.createElement('div');
            div.classList.add('card', 'bg-base-100', 'shadow');
            div.innerHTML = `
                <div class="card-body py-4">
                    <div class="flex justify-between items-center gap-3">
                        <p class="flex-1">${escapeHtml(joke.joke)}</p>
                        <button
                            data-id="${joke.id}"
                            data-joke="${escapeAttr(joke.joke)}"
                            class="save-btn btn btn-success btn-xs"
                        >
                        <span class="material-symbols-outlined" style="font-size: 14px;">bookmark</span>
                            Save
                        </button>
                    </div>
                </div>
            `;
            return div;
        }

        function escapeHtml(str) {
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function escapeAttr(str) {
            return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        async function doSearch(term, page = 1) {
            currentTerm = term;
            currentPage = page;

            container.innerHTML = '';
            statusBar.classList.add('hidden');
            loader.classList.remove('hidden');
            loader.style.display = 'flex';

            try {
                const url = `${API_BASE}/search?term=${encodeURIComponent(term)}&limit=30&page=${page}`;
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'User-Agent': 'DadJoker App (https://github.com/katp1998)',
                    },
                });

                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();
                totalPages = data.total_pages;

                loader.style.display = 'none';

                if (data.results.length === 0) {
                    statusText.textContent = `No jokes found for "${term}".`;
                    statusBar.classList.remove('hidden');
                    prevBtn.disabled = true;
                    nextBtn.disabled = true;
                    return;
                }

                //status bar
                statusText.textContent =
                    `${data.total_jokes} joke${data.total_jokes !== 1 ? 's' : ''} found for "${term}"`;
                pageIndicator.textContent = `Page ${data.current_page} of ${data.total_pages}`;
                prevBtn.disabled = data.current_page <= 1;
                nextBtn.disabled = data.current_page >= data.total_pages;
                statusBar.classList.remove('hidden');

                data.results.forEach(j => container.appendChild(buildCard(j)));

                //save listeners
                container.querySelectorAll('.save-btn').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        btn.disabled = true;
                        btn.textContent = '...';

                        try {
                            const r = await fetch(SAVE_URL, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    joke:   btn.dataset.joke,
                                    api_id: btn.dataset.id,
                                }),
                            });
                            const d = await r.json();
                            showToast(d.message);
                            btn.textContent = 'Saved!';
                        } catch {
                            showToast('Failed to save the joke', true);
                            btn.disabled = false;
                            btn.textContent = 'Save';
                        }
                    });
                });

            } catch {
                loader.style.display = 'none';
                statusText.textContent = 'Oops! Something went wrong. Please try again.';
                statusBar.classList.remove('hidden');
            }
        }

        searchBtn.addEventListener('click', () => {
            const term = searchInput.value.trim();
            if (term) doSearch(term, 1);
        });

        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') searchBtn.click();
        });

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) doSearch(currentTerm, currentPage - 1);
        });

        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) doSearch(currentTerm, currentPage + 1);
        });
    </script>
</x-layout>