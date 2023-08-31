<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\EncaissementElevesPl;
use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class VersementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}', 'forgetpassword']]);
    }

    //3 derniere versements de l'eleves de lannee en cours
    public function versements($Matri_Elev)
    {
        $currentYear = date('Y');
        $etudiants = Etudiant::where('Matri_Elev', $Matri_Elev)->first();
        if (!$etudiants) {
            return Response()->json(
                [
                    'success' => false,
                    'status' => 404,
                    'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
                ]
            );
        }
        $versements = DB::table('Encaissements des Elèves Pl as En')
            ->selectRaw('En.Date_Encais, En.Matri_Elev, En.Ancien_sold, En.Montant_Encais, En.Nouveau_sold, En.anneeScolEncaissElevPL, etudiants.Code_Detcla')
            ->join('etudiants', 'En.Matri_Elev', '=', 'etudiants.Matri_Elev')
            ->where('En.Matri_Elev', $Matri_Elev)
            ->where('En.anneeScolEncaissElevPL', 'LIKE', '%' . $currentYear . '%')
            ->latest('En.Date_Encais')
            ->take(3)
            ->get();

        if ($versements->isEmpty()) {

            $versements = DB::table('Historique_Encaissement_Pl as Hen')
                ->selectRaw('Hen.Date_Encais, Hen.Matri_Elev, Hen.Ancien_sold, Hen.Montant_Encais, Hen.Nouveau_sold, Hen.anneeScolEncaissElevPL, etudiants.Code_Detcla')
                ->join('etudiants', 'Hen.Matri_Elev', '=', 'etudiants.Matri_Elev')
                ->where('Hen.Matri_Elev', $Matri_Elev)
                ->where('Hen.Annee_scolaire', 'LIKE', '%' . $currentYear . '%')
                ->latest('Hen.Date_Encais')
                ->take(3)
                ->get();

            if ($versements->isEmpty()) {
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun versement trouvé pour l\'année académique ' . $currentYear
                    ]

                );
            }
            return $this->verssements($versements);
        }
        return $this->verssements($versements);
    }

    //tous les versements de l'eleves de lannee en cours

    public function all_versements($Matri_Elev)
    {
        $currentYear = date('Y');
        $etudiants = Etudiant::where('Matri_Elev', $Matri_Elev)->first();
        if (!$etudiants) {
            return Response()->json(
                [
                    'success' => false,
                    'status' => 404,
                    'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
                ]
            );
        }
        $versements = DB::table('Encaissements des Elèves Pl as En')
            ->selectRaw('En.Date_Encais, En.Matri_Elev, En.Ancien_sold, En.Montant_Encais, En.Nouveau_sold, En.anneeScolEncaissElevPL, etudiants.Code_Detcla')
            ->join('etudiants', 'En.Matri_Elev', '=', 'etudiants.Matri_Elev')
            ->where('En.Matri_Elev', $Matri_Elev)
            ->where('En.anneeScolEncaissElevPL', 'LIKE', '%' . $currentYear . '%')
            ->latest('En.Date_Encais')
            ->get();

        if ($versements->isEmpty()) {

            $versements = DB::table('Historique_Encaissement_Pl as Hen')
                ->selectRaw('Hen.Date_Encais, Hen.Matri_Elev, Hen.Ancien_sold, Hen.Montant_Encais, Hen.Nouveau_sold, Hen.anneeScolEncaissElevPL, etudiants.Code_Detcla')
                ->join('etudiants', 'Hen.Matri_Elev', '=', 'etudiants.Matri_Elev')
                ->where('Hen.Matri_Elev', $Matri_Elev)
                ->where('Hen.Annee_scolaire', 'LIKE', '%' . $currentYear . '%')
                ->latest('Hen.Date_Encais')
                ->get();

            if ($versements->isEmpty()) {
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun versement trouvé pour l\'année académique ' . $currentYear
                    ]

                );
            }
            return $this->verssements($versements);
        }
        return $this->verssements($versements);
    }

    //versements de l'eleves des années precedentes

    public function last_versements($Matri_Elev)
    {
        $anneeCourante = date('Y');
        $etudiants = Etudiant::where('Matri_Elev', $Matri_Elev)->first();
        if (!$etudiants) {
            return Response()->json(
                [
                    'success' => false,
                    'status' => 404,
                    'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
                ]
            );
        }
        $versements = DB::table('Historique_Encaissement_Pl as Hen')
            ->selectRaw('Hen.Date_Encais, Hen.Matri_Elev, Hen.Ancien_sold, Hen.Montant_Encais, Hen.Nouveau_sold, Hen.Annee_scolaire, etudiants.Code_Detcla')
            ->join('etudiants', 'Hen.Matri_Elev', '=', 'etudiants.Matri_Elev')
            ->where('Hen.Matri_Elev', $Matri_Elev)
            ->whereRaw('SUBSTRING(Hen.Annee_scolaire, 1, 4) < ?', $anneeCourante)
            ->latest('Hen.Date_Encais')
            ->get();

        if ($versements->isEmpty()) {
            return response()->json(
                [
                    'success'  => false,
                    'status' => 401,
                    'message' => 'Aucun versement historisé'
                ]

            );
        }
        return $this->verssements($versements);
    }

    protected function verssements($data)
    {
        return response()->json(
            [
                'success'  => true,
                'status' => 200,
                'versements' => $data
            ]

        );
    }
}
