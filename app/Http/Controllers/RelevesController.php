<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class RelevesController extends Controller
{
    public function getannee($Matri_Elev)
    {
        $etudiants = Etudiant::where('Matri_Elev', $Matri_Elev)->first();

        if (!$etudiants) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
            ]);
        }
        return $this->getAnneeDeliberer($Matri_Elev);
    }


    protected function getAnneeDeliberer($id)
    {
        $etudiant = DB::table('DELIBERER as delib')
            ->selectRaw('DISTINCT(delib.Annee) as Annee')
            ->join('etudiants as etud', 'delib.Matri_Elev', '=', 'etud.Matri_Elev')
            ->join('UE', 'delib.CodeUE', '=', 'UE.CodeUE')
            ->join('GROUPE as group', 'delib.IdGroupe', '=', 'group.IdGroupe')
            ->join('Examen as exam', 'group.Code_Examen', '=', 'exam.Code_Examen')
            ->join('ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE')
            ->where('delib.Matri_Elev', $id)
            ->pluck('Annee');

        if ($etudiant->isEmpty()) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Aucun bulletin disponible pour cet etudiant',
            ]);
        }
        $classeParAnnee = [];

        foreach ($etudiant as $annee) {
            $annee = trim($annee);
            $classeParAnnee[$annee] = $this->getclasse($id, $annee);
        }
        if (!empty($classeParAnnee)) {
            return Response()->json([
                'success' => true,
                'status' => 200,
                'classes' => $classeParAnnee,
            ]);
        } else {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'Aucun bulletin disponible pour cet étudiant',
            ]);
        }
    }
    protected function getclasse($matricule, $etudiantannee)
    {
        $etud = DB::table('Historique as histo')
            ->selectRaw('cla.Code_Cla, histo.AnneeSco_Elev as annee')
            ->join('Détail Classes as detcla', 'detcla.Code_Detcla', '=', 'histo.Code_Detcla')
            ->join('Classes Info Générales as cla', 'detcla.Code_Cla', '=', 'cla.Code_Cla')
            ->where('histo.Matri_Elev', $matricule)
            ->where('histo.AnneeSco_Elev', $etudiantannee)
            ->get();
        return $etud;
    }
}