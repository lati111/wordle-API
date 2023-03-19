<?php

namespace App\Models\Wordle;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'wordle_session';
    protected $primaryKey='uuid';
    protected $keyType = 'uuid';

    public function Owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }
}
