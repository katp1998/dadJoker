<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Joke;

class JokePolicy
{
    public function update(User $user, Joke $joke): bool {
        return $joke->user()->is($user);
    }

    public function delete(User $user, Joke $joke){
        return $joke->user()->is($user);
    }
}
