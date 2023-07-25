<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\Eleves as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Eleve extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table = 'Elèves';

    protected $primaryKey = 'Matri_Elev';

    protected $fillable = [
        'Matri_Elev',
        'Nom_Elev',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'Matri_Elev' => 'string',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function encaissementelevespls()
    {
        return $this->hasMany(EncaissementElevesPl::class);
    }
}
