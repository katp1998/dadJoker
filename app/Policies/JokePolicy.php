<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Joke;

class JokePolicy
{
    //ensure noone other than the user can update/delete joke associated with a user:
    public function update(User $user, Joke $joke): bool
    {
        return $joke->user()->is($user);
    }

    public function delete(User $user, Joke $joke): bool
    {
        return $joke->user()->is($user);
    }
}
