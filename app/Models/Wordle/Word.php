<?php

namespace App\Models\Wordle;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Word extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'wordle__word';
    protected $primaryKey = 'uuid';
    protected $keyType = 'uuid';

    public function Session(): BelongsTo
    {
        return $this->belongsTo(Session::class, "uuid");
    }
}
