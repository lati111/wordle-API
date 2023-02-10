<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email_Verify extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'email_verify';
    protected $primaryKey = 'token';
}
