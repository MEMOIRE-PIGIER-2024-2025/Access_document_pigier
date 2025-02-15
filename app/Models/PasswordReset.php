<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    protected $table = 'password_reset_tokens';
    protected $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];
}
