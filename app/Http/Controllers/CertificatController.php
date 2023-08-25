<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Historique;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class CertificatController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}', 'forgetpassword']]);
    }

    //certificat inscription et scolarité
    public function getCertificats($Matri_Elev)
    {
        $user = Eleve::find($Matri_Elev);

        $currentYear = date('Y');


        if (!$user) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => "utilisateur inconnu",
            ], 404);
        } else {
            //Certificate de frequentation et d'inscription
            $certificat = DB::table('Elèves as Elev')
                ->selectRaw('Elev.Nom_Elev, Elev.Datenais_Elev, Elev.Lieunais_Elev, Elev.Actenais_Elev, Elev.Matri_Elev, Cla.Nom_Cla, Elev.AnneeSco_Elev, GETDATE() as datejour')
                ->join('Détail Classes as detcla', 'detcla.Code_Detcla', '=', 'Elev.Code_Detcla')
                ->join('Classes Info Générales as cla', 'detcla.Code_Cla', '=', 'cla.Code_Cla')
                ->where('Elev.Matri_Elev', $Matri_Elev)
                ->where('Elev.AnneeSco_Elev', 'LIKE', '%' . $currentYear . '%')
                ->get();


            if ($certificat->isEmpty()) {
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun certificat trouvé pour l\'année académique ' . $currentYear
                    ],
                    401
                );
            }
            return response()->json(
                [
                    'success'  => true,
                    'status' => 200,
                    'certificat inscription' => $certificat,
                ],
                200
            );
        }
    }

    public function getCertificats_scol($Matri_Elev)
    {
        $user = Historique::find($Matri_Elev);

        $currentYear = date('Y');

        if (!$user) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => "utilisateur inconnu",
            ], 404);
        } else {

            //code

        }
    }
}
