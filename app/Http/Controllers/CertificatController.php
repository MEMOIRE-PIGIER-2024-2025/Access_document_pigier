<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Etudiant;
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
        $user = Etudiant::where('Matri_Elev', $Matri_Elev)->first();

        $currentYear = date('Y');


        if (!$user) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
            ]);
        } else {
            //Certificate de frequentation et d'inscription
            $certificat = DB::table('Elèves as Elev')
                ->selectRaw('Elev.Nom_Elev, Elev.Lieunais_Elev, Elev.Actenais_Elev, Elev.Matri_Elev, Cla.Nom_Cla, Elev.AnneeSco_Elev')
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, Elev.Datenais_Elev) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  Elev.Datenais_Elev) AS NVARCHAR(2)), 2), '/', DATEPART(year, Elev.Datenais_Elev)) as Datenais_Elev")
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day,  GETDATE()) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  GETDATE()) AS NVARCHAR(2)), 2), '/', DATEPART(year, GETDATE())) as datejour")

                ->join('Détail Classes as detcla', 'detcla.Code_Detcla', '=', 'Elev.Code_Detcla')
                ->join('Classes Info Générales as cla', 'detcla.Code_Cla', '=', 'cla.Code_Cla')
                ->where('Elev.Matri_Elev', $Matri_Elev)
                //->where('Elev.AnneeSco_Elev', 'LIKE', '%' . $currentYear . '%')
                ->get();


            if ($certificat->isEmpty()) {
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun certificat trouvé pour l\'année académique ' . $currentYear
                    ]

                );
            }
            return $this->certificats($certificat);
        }
    }

    public function getCertificats_scol($Matri_Elev)
    {
        $user = Etudiant::where('Matri_Elev', $Matri_Elev)->first();
        if (!$user) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
            ]);
        } else {
            //Certificate de scolarité
            $certificat_scol = DB::table('Historique as Hist')
                ->selectRaw('Hist.Nom_Elev, Hist.Lieunais_Elev, Hist.Actenais_Elev, Hist.DateEntre_Elev, Hist.Matri_Elev, Cla.Nom_Cla, Hist.AnneeSco_Elev')
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, Hist.Datenais_Elev) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month, Hist.Datenais_Elev) AS NVARCHAR(2)), 2), '/', DATEPART(year,Hist.Datenais_Elev)) as Datenais_Elev")
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day,  GETDATE()) AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month,  GETDATE()) AS NVARCHAR(2)), 2), '/', DATEPART(year, GETDATE())) as datejour")

                ->join('Détail Classes as detcla', 'detcla.Code_Detcla', '=', 'Hist.Code_Detcla')
                ->join('Classes Info Générales as cla', 'detcla.Code_Cla', '=', 'cla.Code_Cla')
                ->where('Hist.Matri_Elev', $Matri_Elev)
                ->where('Hist.SoldSco_Elev', 0)
                ->orderBy('Hist.AnneeSco_Elev', 'asc')
                ->get();
            $firstDateEntreElev = $certificat_scol->first()->DateEntre_Elev;

            $certificat_scol_Date = DB::table('Historique as Hist')
                ->selectRaw("CONCAT(RIGHT('00' + CAST(DATEPART(day, '$firstDateEntreElev') AS NVARCHAR(2)), 2), '/', RIGHT('00' + CAST(DATEPART(month, '$firstDateEntreElev') AS NVARCHAR(2)), 2), '/', DATEPART(year,'$firstDateEntreElev')) as Debut")
                ->selectRaw("CONCAT('30/06/',RIGHT(Hist.AnneeSco_Elev, 4)) as Fin")
                ->where('Hist.Matri_Elev', $Matri_Elev)
                ->where('Hist.SoldSco_Elev', 0)
                ->orderBy('Hist.AnneeSco_Elev', 'asc')
                ->get()
                ->last();




            if ($certificat_scol->isEmpty()) {
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 401,
                        'message' => 'Aucun certificat trouvé',
                    ]

                );
            }

            return $this->certificatsDate($certificat_scol_Date, $certificat_scol);
        }
    }

    protected function certificats($data)
    {

        return response()->json(
            [
                'success'  => true,
                'status' => 200,
                'certificats' => $data
            ]

        );
    }
    protected function certificatsDate($date, $data)
    {

        return response()->json(
            [
                'success'  => true,
                'status' => 200,
                'date' => $date,
                'certificats' => $data
            ]

        );
    }
}
