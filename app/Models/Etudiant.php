<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Etudiant as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'etudiants';

    protected $fillable = [
        'Matri_Elev',
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
        'MoySemPremier',
        'MoySemDeuxieme',
        'MoyAnnuelle',
        'TotalCreditSemPremier',
        'TotalCreditSemDeuxieme',
        'rememberToken',
        'password',
        'active',
        'created_at',
        'updated_at',

    ];

    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'Matri_Elev' => 'string',
        'Datenais_Elev' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',


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
