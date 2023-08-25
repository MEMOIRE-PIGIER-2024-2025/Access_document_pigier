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
        'Num_Fol',
        'Date_Encais',
        'Objet_Encais',
        'Matri_Elev',
        'Mode_Reg',
        'Ancien_Sold',
        'Montant_Encais',
        'Montant_Encais_Ste',
        'Nouveau_Sold',
        'CodeMotifEnc',
        'caissiaire',
        'CodeTarifExam',
        'CodeTarif',
        'CodeTarifSte',
        'cotisation',
        'id_caissiaire',
        'NumBord',
        'fraisExamen',
        'reservation',
        'Ancien_Sold_Frais_Exam',
        'Nouveau_Sold_Frais_Exam',
        'Code_Ste',
        'Ancien_Sold_Ste',
        'Nouveau_Sold_Ste',
        'Ref_Trans_Mobile',
        'Tel_Trans_Mobile',
        'Date_Trans_Mobile',
        'Regularisation',
        'Solde_save',
        'NumGuichet',
        'anneeScolEncaissElevPL',
        'CodeAcces',
        'CodeUE',
        'Montant_Anc_Access',
        'Ancien_Solde_Access',
        'Nouveau_Sold_Access',
        'Nom_Prenoms_Eleve',
        'Etablissement_Source',
        'Classe_Eleve',
        'Montant_Accessoire_Due',
        'codeCarteBancaire',
        'numAutorisationBancaire',
    ];
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'Matri_Elev' => 'string',
    ];


    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }
}
