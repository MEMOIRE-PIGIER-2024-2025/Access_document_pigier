<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncaissementElevesPl extends Model
{
    use HasFactory;
    protected $table = 'Encaissements des Elèves Pl';

    protected $primaryKey = 'Num_Encais';

    protected $fillable = [
        'Num_Encais'
    ];
    protected $hidden = [
        'password',
    ];


    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }
}
