<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Joke extends Model
{
    protected $fillable = [
        'joke',
        'api_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}