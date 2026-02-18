@props(['joke'])

<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex space-x-3">
            @if ($joke->user)
                <div class="avatar">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/{{ urlencode($joke->user->email) }}"
                            alt="{{ $joke->user->name }}'s avatar" class="rounded-full" />
                    </div>
                </div>
            @else
                <div class="avatar placeholder">
                    <div class="size-10 rounded-full bg-base-300 flex items-center justify-center">
                         <img src="<https://avatars.laravel.cloud/f61123d5-0b27-434c-a4ae-c653c7fc9ed6?vibe=stealth>"
                            alt="boo" class="rounded-full" />
                    </div>
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <div class="flex justify-between w-full">
                    <div class="flex items-center gap-1">
                        <span class="text-sm font-semibold">
                            {{ $joke->user ? $joke->user->name : 'Anonymous' }}
                        </span>
                        <span class="text-base-content/60">·</span>
                        <span class="text-sm text-base-content/60">
                            {{ $joke->created_at->diffForHumans() }}
                        </span>
                        @if ($joke->updated_at->gt($joke->created_at->addSeconds(5)))
                            <span class="text-base-content/60">·</span>
                            <span class="text-sm text-base-content/60 italic">edited</span>
                        @endif
                    </div>

                    @can('update', $joke)
                        <div class="flex gap-1">
                            <a href="{{ route('jokes.edit', $joke) }}" class="btn btn-ghost btn-xs">Edit</a>
                            <form method="POST" action="{{ route('jokes.destroy', $joke) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('Delete this joke?')"
                                    class="btn btn-ghost btn-xs text-error">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>

                <p class="mt-2">{{ $joke->joke }}</p>
            </div>
        </div>
    </div>
</div>