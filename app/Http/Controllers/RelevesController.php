<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RelevesController extends Controller {
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}', 'forgetpassword']]);
    }
//rechercher la classe de l'etudiant

        public function getannee( $Matri_Elev ) {
        $etudiants = Etudiant::where( 'Matri_Elev', $Matri_Elev )->first();

        if ( !$etudiants ) {
            return Response()->json( [
                'success' => false,
                'status' => 404,
                'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
            ]);
        }
        return $this->getAnneeDeliberer($Matri_Elev);
    }


        protected function getAnneeDeliberer($id)
    {
        $annees_academiques = DB::table('DELIBERER as delib')
        ->select('delib.Annee')
        ->where('delib.Matri_Elev', $id)
        ->distinct()
        ->get();
    
        $annees_formattees = [];
        
        foreach ($annees_academiques as $annee) {
            $annee_formattee = trim(str_replace('/', '-', $annee->Annee));
            $annees_formattees[] = $annee_formattee;
        }
                if ($annees_academiques->isEmpty() ) {
                    return Response()->json( [
                        'success' => false,
                        'status' => 404,
                        'message' => 'Aucun bulletin disponible pour cet etudiant',
                    ] );
                }
                else{
                    return Response()->json( [
                        'success' => true,
                        'status' => 200,
                        'Annee_Academique' => $annees_formattees,
                   ] );
                }
    }
     public function getreleve($Matri_Elev,$annee_acad)
     {
        //retourner le semestre
        $semestre  = DB::table('DELIBERER as delib')
            ->selectRaw('sem.Code_semestre as semestre')
            ->join('etudiants as etud', 'delib.Matri_Elev', '=', 'etud.Matri_Elev' )
            ->join( 'UE', 'delib.CodeUE', '=', 'UE.CodeUE' )
            ->join( 'GROUPE as group', 'delib.IdGroupe', '=', 'group.IdGroupe' )
            ->join( 'Examen as exam', 'group.Code_Examen', '=', 'exam.Code_Examen' )
            ->join( 'ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE' )
            ->join( 'SEMESTRE as sem', 'exam.Code_semestre', '=', 'sem.Code_semestre' )
            ->where( 'delib.Matri_Elev', $Matri_Elev )
            ->where( 'delib.Annee', trim(str_replace('-', '/', $annee_acad)))
            ->distinct()
            ->pluck('semestre');

            if($semestre->isEmpty())
            {
                return Response()->json( [
                    'success' => false,
                    'status' => 404,
                    'message' => 'Aucun bulletin disponible pour cet etudiant',
                    ] );
            }
            else{
              
                    return Response()->json( [
                        'success' => true,
                        'status' => 200,
                        'semestre' => $semestre,
                        ] );
    

            }

     }
     public function relevesnote($matricule,$annee_acad,$semestre)
{
    
    $results = DB::table('DELIBERER as delib')
    ->selectRaw('sem.Code_semestre, sem.lib_semetre, ECUE.code_ecue, ECUE.lib_ecue, ECUE.coef, delib.Moyenne, delib.Des_Deliber, CONCAT(sess.CODE_SESS, \' \', GROUPE.Annee_Academic) as SessionAnnee')
    ->join('etudiants as etud', 'delib.Matri_Elev', '=', 'etud.Matri_Elev')
    ->join('UE', 'delib.CodeUE', '=', 'UE.CodeUE')
    ->join('GROUPE', 'delib.IdGroupe', '=', 'GROUPE.IdGroupe')
    ->join('Examen as exam', 'GROUPE.Code_Examen', '=', 'exam.Code_Examen')
    ->join('ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE')
    ->join('SEMESTRE as sem', 'exam.Code_semestre', '=', 'sem.Code_semestre')
    ->join('session as sess', 'exam.COD_SESS', '=', 'sess.CODE_SESS')
    ->where('delib.Matri_Elev', $matricule)
    ->where('ECUE.anneeAcademiqueECUE', trim(str_replace('-', '/', $annee_acad)))
    ->where('UE.AnneeAcademiqueUE', trim(str_replace('-', '/', $annee_acad)))
    ->where('delib.Annee', trim(str_replace('-', '/', $annee_acad)))
    ->where('GROUPE.Annee_Academic', trim(str_replace('-', '/', $annee_acad)))
    ->where('sem.Code_semestre', $semestre)
    ->get();

    foreach ($results as $result) {
        if ($result->Des_Deliber === "Ajourné") {
            $codeUE = $result->code_ecue;

            // Exécutez la deuxième requête pour les mêmes critères
            $results_par_session = DB::table('DELIBERER as delib')
            ->selectRaw('ECUE.code_ecue, ECUE.lib_ecue, ECUE.coef, delib.Moyenne, delib.Des_Deliber,CONCAT(sess.CODE_SESS, \' \', GROUPE.Annee_Academic) as SessionAnnee')
            ->join('etudiants as etud', 'delib.Matri_Elev', '=', 'etud.Matri_Elev')
            ->join('UE', 'delib.CodeUE', '=', 'UE.CodeUE')
            ->join('GROUPE', 'delib.IdGroupe', '=', 'GROUPE.IdGroupe')
            ->join('Examen as exam', 'GROUPE.Code_Examen', '=', 'exam.Code_Examen')
            ->join('ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE')
            ->join('session as sess', 'exam.COD_SESS', '=', 'sess.CODE_SESS')
            ->where('delib.Matri_Elev', $matricule)
            ->where('ECUE.code_ecue', $codeUE)
            ->where( 'sess.CODE_SESS', 'Sess2')
            ->orderBy('Moyenne', 'desc')
            ->get();

            if ($results_par_session->isNotEmpty()) {
                // Obtenez le résultat avec la meilleure note
                $bestResult = $results_par_session->first();
    
                // Comparez les notes et mettez à jour si la nouvelle note est meilleure
                
                if ($bestResult->Moyenne > $result->Moyenne) {
                    
                    $result->Moyenne = $bestResult->Moyenne;
                    $result->Des_Deliber = $bestResult->Des_Deliber;
                    $result->SessionAnnee = $bestResult->SessionAnnee;
                }
                if ($bestResult->Moyenne <= $result->Moyenne && $bestResult->Des_Deliber === 'Compensé(e)') {

                    $result->Moyenne = $bestResult->Moyenne;
                    $result->Des_Deliber = $bestResult->Des_Deliber;
                    $result->SessionAnnee = $bestResult->SessionAnnee;
                }
            }
    
        }
    }

        // Initialisez deux tableaux pour les UE majeures et mineures
        $ueMajeur = [];
        $ueMineur = [];

        foreach ($results as $result) {
            if ($result->coef >= 4) {
                // Ajoutez l'UE à la liste des UE majeures
                $ueMajeur[] = $result;
            } else {
                // Ajoutez l'UE à la liste des UE mineures
                $ueMineur[] = $result;
            }
        }

            // Calcul de la somme totale des coefficients pour les UE majeures et mineures
            $totalCoefMajeur = array_sum(array_column($ueMajeur, 'coef'));
            $totalCoefMineur = array_sum(array_column($ueMineur, 'coef'));

            // Calcul de la moyenne pondérée pour les UE majeures et mineures
            $moyennePondereeMajeur = array_sum(array_map(function ($ue) {
                return $ue->Moyenne * $ue->coef;
            }, $ueMajeur)) / $totalCoefMajeur;

            $moyennePondereeMineur = array_sum(array_map(function ($ue) {
                return $ue->Moyenne * $ue->coef;
            }, $ueMineur)) / $totalCoefMineur;



            // Maintenant, exécutez la requête pour les informations de l'élève
            $class_elev = DB::table('Elèves as Elev')
            ->selectRaw('Elev.Nom_Elev, Elev.Lieunais_Elev, Elev.Matri_Elev, Cla.Nom_Cla')
            ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, Elev.Datenais_Elev) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month, Elev.Datenais_Elev) AS NVARCHAR(2)), 2), '/', DATEPART(year, Elev.Datenais_Elev)) as Datenais_Elev")
            ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, GETDATE()) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month, GETDATE()) AS NVARCHAR(2)), 2), '/', DATEPART(year, GETDATE())) as datejour")
            ->join('Détail Classes as detcla', 'detcla.Code_Detcla', '=', 'Elev.Code_Detcla')
            ->join('Classes Info Générales as cla', 'detcla.Code_Cla', '=', 'cla.Code_Cla')
            ->where('Elev.Matri_Elev', $matricule)
            ->where('Elev.AnneeSco_Elev', trim(str_replace('-', '/', $annee_acad)))
            ->first();

            // Si la première requête ne renvoie pas de résultat, alors effectuez la deuxième requête
            if (!$class_elev) {
            $class_elev = DB::table('Historique as Hist')
                ->selectRaw('Hist.Nom_Elev, Hist.Lieunais_Elev, cla.Nom_Cla, Hist.Matri_Elev')
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, Hist.Datenais_Elev) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month, Hist.Datenais_Elev) AS NVARCHAR(2)), 2), '/', DATEPART(year,Hist.Datenais_Elev)) as Datenais_Elev")
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, GETDATE()) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month, GETDATE()) AS NVARCHAR(2)), 2), '/', DATEPART(year, GETDATE())) as datejour")
                ->join('Détail Classes as detcla', 'detcla.Code_Detcla', '=', 'Hist.Code_Detcla')
                ->join('Classes Info Générales as cla', 'detcla.Code_Cla', '=', 'cla.Code_Cla')
                ->where('Hist.Matri_Elev', $matricule)
                ->where('Hist.AnneeSco_Elev', trim(str_replace('-', '/', $annee_acad)))
                ->first();
            }


            // Construire un tableau avec les résultats
            $jsonResult = [
                'UE_Majeures' => [
                    'SommeTotaleCoef' => $totalCoefMajeur,
                    'MoyennePonderee' => $moyennePondereeMajeur,
                    'UEs' => $ueMajeur,
                ],
                'UE_Mineures' => [
                    'SommeTotaleCoef' => $totalCoefMineur,
                    'MoyennePonderee' => $moyennePondereeMineur,
                    'UEs' => $ueMineur,
                ],
            ];
            $jsonResult['Etudiant'] = $class_elev;

        // // Convertir le tableau en JSON
        //     $json = json_encode($jsonResult);

            // Retourner l'objet JSON
            return response()->json([
                'success' => true,
                'status' => 200,
                'Bulletin_par_semestre' => $jsonResult,
                ]);
}


 }