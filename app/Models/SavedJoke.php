<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedJoke extends Model
{
    protected $fillable = [
        'user_id',
        'api_id',
        'joke',
        'note'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
