<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    use HasFactory;

    protected $table = 'personal_access_tokens';
    protected $primaryKey = 'id';

    protected $fillable = [
        'expires_at'
    ];

    public static function findToken($token)
    {
        if (strpos($token, '|') === false) {
            return static::where('token', hash('sha256', $token))->first();
        }

        [$id, $token] = explode('|', $token, 2);

        if ($instance = static::find($id)) {
            return hash_equals($instance->token, hash('sha256', $token)) ? $instance : null;
        }
    }

    public function Owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tokenable_id');
    }


}
