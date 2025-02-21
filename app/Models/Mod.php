<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mod extends Model
{
    use HasFactory;

    /**
     * The game this mod is made for.
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
