<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Models\Joke;

class JokeController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jokes = auth()->user()->jokes()
            ->with('user')
            ->latest()
            ->get();

        return view('home', ['jokes' => $jokes]);
    }

    public function create(){
        return view('jokes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'joke' => 'required|string|max:500',
        ]);
        auth()->user()->jokes()->create($validated);

        return redirect('/home')->with('success', 'Joke saved!');
    }

    /**
     * Save the specified resource.
     */
    public function saveFromApi(Request $request)
    {
        $validated = $request->validate([
            'joke'   => 'required|string|max:500',
            'api_id' => 'nullable|string|max:50',
        ]);

        //prevent duplicates
        if (!empty($validated['api_id'])) {
            $exists = auth()->user()->jokes()
                ->where('api_id', $validated['api_id'])
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Already in your collection!'], 200);
            }
        }

        $joke = auth()->user()->jokes()->create([
            'joke'   => $validated['joke'],
            'api_id' => $validated['api_id'] ?? null,
        ]);

        return response()->json(['message' => 'Joke saved!', 'joke' => $joke], status: 201);
    }

    public function edit(Joke $joke)
    {
        $this->authorize('update', $joke);
        return view('jokes.edit', compact('joke'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Joke $joke)
    {
        $this->authorize('update', $joke);
        $validated = $request->validate([
            'joke'=>'required|string|max:500'
        ]);
        $joke->update($validated);
        return redirect('/home')->with('success', 'Joke updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Joke $joke)
    {
        $this->authorize('delete', $joke);
        $joke->delete();
        return redirect('/home')->with('success', 'Joke deleted!');
    }

    public function search()
    {
        return view('jokes.search');
    }
}
