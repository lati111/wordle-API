<?php

namespace App\Models\Wordle;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'wordle__session';
    protected $primaryKey='uuid';
    protected $keyType = 'uuid';


        public function Words(): HasMany
    {
        return $this->hasMany(Word::class, 'session');
    }
}
