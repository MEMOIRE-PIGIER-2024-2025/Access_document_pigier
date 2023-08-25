<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\Eleves as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Eleve extends Model
{
    use HasFactory;
    protected $table = 'Elèves';

    protected $primaryKey = 'Matri_Elev';

    protected $fillable = [
        'Code_Grp',
        'Nom_Elev',
        'Sexe_Elev',
        'Code_Ste',
        'Code_Eta',
        'Sub_Eta',
        'Code_Reg',
        'Code_Brs',
        'Code_Opt',
        'Mont_Opt',
        'Code_Detcla',
        'Nom_Cla',
        'Code_Nat',
        'Lieunais_Elev',
        'Datenais_Elev',
        'TelBurResp_Elev',
        'Actenais_Elev',
        'NomPere_Elev',
        'NomMere_Elev',
        'EtablOrig_Elev',
        'ClassOrig_Elev',
        'AnneeSco_Orig',
        'DateEntre_Elev',
        'Code_Niv',
        'AnneeSco_Elev',
        'SerieBac_Elev',
        'DateObtBac_Elev',
        'Redouble_Elev',
        'Cycle_Elev',
        'NomResp_Elev',
        'ProfResp_Elev',
        'TitreResp_Elev',
        'AdresResp_Elev',
        'VilleResp_Elev',
        'TelDomResp_Elev',
        'MontantSco_Elev',
        'SoldSco_Elev',
        'Remise_Elev',
        'Bourse_Elev',
        'SoldBourse_Elev',
        'DejaRegle',
        'Photo',
        'Comment',
        'Condition_Elev',
        'Cotisation',
        'SoldFraisExam',
        'Prenom_Elev',
        'scolFDFP',
        'idFDFP',
        'idPreinscription',
        'Reservation',
        'numtabl',
        'numatri',
        'totbac',
        'matpc',
        'celetud',
        'teletud',
        'villetud',
        'cometud',
        'mailetud',
        'reduction',
        'Etab_source',
        'Avoir',
        'natPiece',
        'montantExamen',
        'Admission_Annee_Sup',
        'Cloture_Peda',
        'Extrait_naissance',
        'Photocopie_diplômes',
        'Photocopie_Legalise_BAC',
        'Photocopie_Bulletins',
        'Photo_identité',
        'Fiche_demande_inscription',
        'Fiche_médicale',
        'Cinq_enveloppes',
        'Cinq_timbres',
        'Deuxieme_Extrait_naissance',
        'Deuxieme_Photocopie_Legalise_BAC',
        'DateInscri_Eleve',
        'mailparent',
        'Inscrit_Carte',
        'idperm',
        'Inscrit_Sous_Titre',
        'Inscrit_Sous_Bulletin',
        'rememberToken',
        'password',
        'active',
    ];

    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'Matri_Elev' => 'string',
        // 'Datenais_Elev' => 'datetime',
        // 'DateEntre_Elev' => 'datetime',
        // 'DateInscri_Eleve' => 'datetime',

    ];
}
