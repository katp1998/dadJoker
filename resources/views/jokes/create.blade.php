<x-layout>
    <x-slot:title>Create a Joke</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-3 mt-4 mb-8">
            <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">
                <span class="material-symbols-outlined" style="font-size: 20px;">arrow_back</span>
                Back
            </a>
            <h1 class="text-3xl font-bold">Write a Joke</h1>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <p class="text-base-content/60 mb-4">
                    Want to add your own cringe joke? Here's your chance!
                </p>

                <form method="POST" action="{{ route('jokes.store') }}">
                    @csrf

                    <div class="form-control w-full mb-6">
                        <label class="label" for="joke">
                            <span class="label-text font-semibold">Your Joke</span>
                            <span class="label-text-alt text-xs">|</span>
                            <span class="label-text-alt text-xs">Max 500 characters</span>
                        </label>
                        <textarea
                            id="joke"
                            name="joke"
                            placeholder="I don't know... write something funny..."
                            class="textarea textarea-bordered w-full resize-none @error('joke') textarea-error @enderror"
                            rows="4"
                            maxlength="500"
                            required
                        >{{ old('joke') }}</textarea>
                        @error('joke')
                            <div class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </div>
                        @enderror
                    </div>

                    <div class="card-actions justify-between">
                        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">Save Joke</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>