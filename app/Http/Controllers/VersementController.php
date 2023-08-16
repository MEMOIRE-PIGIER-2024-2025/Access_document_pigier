<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\EncaissementElevesPl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VersementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}', 'forgetpassword']]);
    }

    public function versements($Matri_Elev)
    {
        $currentYear = date('Y');

        $user = Eleve::find($Matri_Elev);



        if (!$user) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => "utilisateur inconnu",
            ], 404);
        } else {

            $versement = DB::table('Elèves')
                ->selectRaw('En.Date_Encais, En.Matri_Elev, En.Ancien_sold, En.Montant_Encais, En.Nouveau_sold, En.anneeScolEncaissElevPL, Elèves.Code_Detcla')
                ->join('Encaissements des Elèves Pl as En', 'Elèves.Matri_Elev', '=', 'En.Matri_Elev')
                ->where('Elèves.Matri_Elev', $Matri_Elev)
                ->where('En.anneeScolEncaissElevPL', 'LIKE', '%' . $currentYear . '%')
                //->whereYear('En.Date_Encais', date('Y'))
                ->latest('En.Date_Encais')
                ->take(3)
                ->get();

            if ($versement->isEmpty()) {


                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun versement trouvé pour l\'année académique ' . $currentYear
                    ],
                    401
                );
            } else {
                return response()->json(
                    [
                        'success'  => true,
                        'status' => 200,
                        'versements' => $versement
                    ],
                    200
                );
            }
        }
    }

    public function all_versements($Matri_Elev)
    {
        $currentYear = date('Y');

        $user = Eleve::find($Matri_Elev);


        if (!$user) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => "utilisateur inconnu",
            ], 404);
        } else {


            $versement = DB::table('Elèves')
                ->selectRaw('En.Date_Encais, En.Matri_Elev, En.Ancien_sold, En.Montant_Encais, En.Nouveau_sold, En.anneeScolEncaissElevPL, Elèves.Code_Detcla')
                ->join('Encaissements des Elèves Pl as En', 'Elèves.Matri_Elev', '=', 'En.Matri_Elev')
                ->where('Elèves.Matri_Elev', $Matri_Elev)
                ->where('En.anneeScolEncaissElevPL', 'LIKE', '%' . $currentYear . '%')
                //->whereYear('En.Date_Encais', date('Y'))
                ->latest('En.Date_Encais')
                ->get();

            if ($versement->isEmpty()) {
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun versement trouvé pour l\'année académique ' . $currentYear
                    ],
                    401
                );
            } else {
                return response()->json(
                    [
                        'success'  => true,
                        'status' => 200,
                        'versements' => $versement
                    ],
                    200
                );
            }
        }
    }

    public function last_versements($Matri_Elev)
    {
        $anneeCourante = date('Y');
        $user = Eleve::find($Matri_Elev);


        if (!$user) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => "utilisateur inconnu",
            ], 404);
        } else {


            $versement = DB::table('Elèves')
                ->selectRaw('Henc.Date_Encais, Henc.Matri_Elev, Henc.Ancien_sold, Henc.Montant_Encais, Henc.Nouveau_sold, Henc.Annee_scolaire, Elèves.Code_Detcla')
                ->join('Historique_Encaissement_Pl as Henc', 'Elèves.Matri_Elev', '=', 'Henc.Matri_Elev')
                ->where('Elèves.Matri_Elev', $Matri_Elev)
                // ->whereYear('Henc.Date_Encais', '<', $anneeCourante)
                ->whereRaw('SUBSTRING(Henc.Annee_scolaire, 1, 4) < ?', $anneeCourante)
                //->groupBy('Henc.Annee_scolaire')
                ->latest('Henc.Date_Encais')
                ->get();

            if ($versement->isEmpty()) {
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun versement antérieur trouvé'
                    ],
                    401
                );
            } else {
                return response()->json(
                    [
                        'success'  => true,
                        'status' => 200,
                        'versements' => $versement
                    ],
                    200
                );
            }
        }
    }
}
