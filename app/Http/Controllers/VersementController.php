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
            ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, En.Date_Encais) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  En.Date_Encais) AS NVARCHAR(2)), 2), '/', DATEPART(year, En.Date_Encais)) as Date_Encais")
            ->selectRaw('En.Ancien_sold, En.Montant_Encais, En.Nouveau_sold, En.anneeScolEncaissElevPL')
            //->join('etudiants', 'En.Matri_Elev', '=', 'etudiants.Matri_Elev')
            ->where('En.Matri_Elev', $Matri_Elev)
            ->where('En.anneeScolEncaissElevPL', 'LIKE', '%' . $currentYear . '%')
            ->latest('En.Date_Encais')
            ->take(3)
            ->get();

        if ($versements->isEmpty()) {

            $versements = DB::table('Historique_Encaissement_Pl as Hen')
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, Hen.Date_Encais) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  Hen.Date_Encais) AS NVARCHAR(2)), 2), '/', DATEPART(year, Hen.Date_Encais)) as Date_Encais")
                ->selectRaw('Hen.Ancien_sold, Hen.Montant_Encais, Hen.Nouveau_sold, Hen.Annee_scolaire')
                //->join('etudiants', 'Hen.Matri_Elev', '=', 'etudiants.Matri_Elev')
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
            ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, En.Date_Encais) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  En.Date_Encais) AS NVARCHAR(2)), 2), '/', DATEPART(year, En.Date_Encais)) as Date_Encais")
            ->selectRaw('En.Ancien_sold, En.Montant_Encais, En.Nouveau_sold, En.anneeScolEncaissElevPL')
            // ->join('etudiants', 'En.Matri_Elev', '=', 'etudiants.Matri_Elev')
            ->where('En.Matri_Elev', $Matri_Elev)
            ->where('En.anneeScolEncaissElevPL', 'LIKE', '%' . $currentYear . '%')
            ->latest('En.Date_Encais')
            ->get();

        if ($versements->isEmpty()) {

            $versements = DB::table('Historique_Encaissement_Pl as Hen')
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, Hen.Date_Encais) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  Hen.Date_Encais) AS NVARCHAR(2)), 2), '/', DATEPART(year, Hen.Date_Encais)) as Date_Encais")
                ->selectRaw('Hen.Ancien_sold, Hen.Montant_Encais, Hen.Nouveau_sold, Hen.Annee_scolaire')
                //->join('etudiants', 'Hen.Matri_Elev', '=', 'etudiants.Matri_Elev')
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
            ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, Hen.Date_Encais) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  Hen.Date_Encais) AS NVARCHAR(2)), 2), '/', DATEPART(year, Hen.Date_Encais)) as Date_Encais")
            ->selectRaw('Hen.Ancien_sold, Hen.Montant_Encais, Hen.Nouveau_sold, Hen.Annee_scolaire as anneeScolEncaissElevPL')
            //->join('Historique as histo', 'Hen.Matri_Elev', '=', 'histo.Matri_Elev')
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
