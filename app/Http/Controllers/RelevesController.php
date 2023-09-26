<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class RelevesController extends Controller {
    // public function getannee( $Matri_Elev ) {
    //     $etudiants = Etudiant::where( 'Matri_Elev', $Matri_Elev )->first();

    //     if ( !$etudiants ) {
    //         return Response()->json( [
    //             'success' => false,
    //             'status' => 404,
    //             'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
    //         ]);
    //     }
    //     return $this->getAnneeDeliberer($Matri_Elev);
    // }


    // protected function getAnneeDeliberer($id)
    // {
    //     $etudiant = DB::table('DELIBERER as delib')
    //         ->selectRaw('DISTINCT( delib.Annee ) as Annee')
    //         ->join('etudiants as etud', 'delib.Matri_Elev', '=', 'etud.Matri_Elev' )
    //             ->join( 'UE', 'delib.CodeUE', '=', 'UE.CodeUE' )
    //             ->join( 'GROUPE as group', 'delib.IdGroupe', '=', 'group.IdGroupe' )
    //             ->join( 'Examen as exam', 'group.Code_Examen', '=', 'exam.Code_Examen' )
    //             ->join( 'ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE' )
    //             ->where( 'delib.Matri_Elev', $id )
    //             ->pluck( 'Annee' );

    //             if ( $etudiant->isEmpty() ) {
    //                 return Response()->json( [
    //                     'success' => false,
    //                     'status' => 404,
    //                     'message' => 'Aucun bulletin disponible pour cet etudiant',
    //                 ] );
    //             }
    //             $classeParAnnee = [];

    //             foreach ( $etudiant as $annee ) {
    //                 $annee = trim( $annee );
    //                 $classeParAnnee[ $annee ] = $this->getclasse( $id, $annee );
    //             }
    //             if ( !empty( $classeParAnnee ) ) {
    //                 return Response()->json( [
    //                     'success' => true,
    //                     'status' => 200,
    //                     'classes' => $classeParAnnee,
    //                 ] );
    //             } else {
    //                 return Response()->json( [
    //                     'success' => false,
    //                     'status' => 404,
    //                     'message' => 'Aucun bulletin disponible pour cet étudiant',
    //                 ] );
    //             }
    //         }
    //         protected function getclasse( $matricule, $etudiantannee ) {
    //             $etud = DB::table( 'Historique as histo' )
    //             ->selectRaw( 'cla.Code_Cla, histo.AnneeSco_Elev as annee' )
    //             ->join( 'Détail Classes as detcla', 'detcla.Code_Detcla', '=', 'histo.Code_Detcla' )
    //             ->join( 'Classes Info Générales as cla', 'detcla.Code_Cla', '=', 'cla.Code_Cla' )
    //             ->where( 'histo.Matri_Elev', $matricule )
    //             ->where( 'histo.AnneeSco_Elev', $etudiantannee )
    //             ->get();
    //             return $etud;
    //         }

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
            
            //retourner la session

            $sessions = DB::table('DELIBERER as delib')
             ->select('exam.Code_Examen as session_annee')
            // ->selectRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(group.IdGroupe, '-', -2), '-', 1) as annee")
            //->selectRaw('CONCAT(sess.CODE_SESS, "-", SUBSTRING(delib.IdGroupe, CHARINDEX(\'-\', delib.IdGroupe) + 1, 4)) as session_annee')
            //->selectRaw('SUBSTRING(delib.IdGroupe, LEN(delib.IdGroupe) - 3 + 1, 4)as session_annee')
            ->join('etudiants as etud', 'delib.Matri_Elev', '=', 'etud.Matri_Elev' )
            ->join( 'UE', 'delib.CodeUE', '=', 'UE.CodeUE' )
            ->join( 'GROUPE as [group]', 'delib.IdGroupe', '=', '[group].IdGroupe' )
            ->join( 'Examen as exam', '[group].Code_Examen', '=', 'exam.Code_Examen' )
            ->join( 'ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE' )
            ->join( 'session as sess', 'exam.COD_SESS', '=', 'sess.CODE_SESS' )
            ->where( 'sess.CODE_SESS', 'Sess2')
            ->where( 'delib.Matri_Elev', $Matri_Elev )
            ->where( 'delib.Annee', trim(str_replace('-', '/', $annee_acad) ))
            ->distinct()
            ->pluck('session_annee');


            if($semestre->isEmpty())
            {
                return Response()->json( [
                    'success' => false,
                    'status' => 404,
                    'message' => 'Aucun bulletin disponible pour cet etudiant',
                    ] );
            }
            else{
                if($sessions->isEmpty())
                {
                return Response()->json( [
                    'success' => true,
                    'status' => 200,
                    'Bulletin' => $semestre
                    ] );
                }
                else{
                    return Response()->json( [
                        'success' => true,
                        'status' => 200,
                        'Bulletin' => $semestre,
                        'session2' => $sessions
                        ] );
    
                }

            }

       // $sessions = [];

        // foreach ($semestre as $code_semestre) {
        //     $sessionsSemestre = DB::table('DELIBERER as delib')
        //         ->select('sess.CODE_SESS as session')
        //         ->join('etudiants as etud', 'delib.Matri_Elev', '=', 'etud.Matri_Elev' )
        //         ->join( 'UE', 'delib.CodeUE', '=', 'UE.CodeUE' )
        //         ->join( 'GROUPE as group', 'delib.IdGroupe', '=', 'group.IdGroupe' )
        //         ->join( 'Examen as exam', 'group.Code_Examen', '=', 'exam.Code_Examen' )
        //         ->join( 'ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE' )
        //         ->join( 'SEMESTRE as sem', 'exam.Code_semestre', '=', 'sem.Code_semestre' )
        //         ->join( 'session as sess', 'exam.COD_SESS', '=', 'sess.CODE_SESS' )
        //         ->where( 'delib.Matri_Elev', $Matri_Elev )
        //         ->where( 'delib.Annee', trim(str_replace('-', '/', $annee_acad) ))
        //         ->distinct()
        //         ->pluck('session');
            
        //     $sessions[$code_semestre] = $sessionsSemestre;
        // }
        // if ( !empty( $sessions ) ) {
        //         return Response()->json( [
        //             'success' => true,
        //             'status' => 200,
        //             'Bulletin' => $sessions
        //             ] );
        // } else {
        //             return Response()->json( [
        //                 'success' => false,
        //                 'status' => 404,
        //                 'message' => 'Aucun bulletin disponible pour cet étudiant',
        //             ] );
        //         }
        //     }

     }
    }