<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class RelevesController extends Controller
{
    public function getreleves($Matri_Elev)
    {
        $etudiants = Etudiant::where('Matri_Elev', $Matri_Elev)->first();

        if (!$etudiants) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => 'L\'élève ayant le matricule ' . $Matri_Elev . ' est introvable.',
            ]);
        }
        $etudiants = DB::table('etudiants as etud')
            ->join('DELIBERER as delib', 'etud.Matri_Elev', '=', 'delib.Matri_Elev')
            ->join('UE', 'delib.CodeUE', '=', 'UE.CodeUE')
            ->join('GROUPE as group', 'delib.IdGroupe', '=', 'group.IdGroupe')
            ->join('Examen as exam', 'group.Code_Examen', '=', 'exam.Code_Examen')
            ->join('ECUE', 'UE.CodeUE', '=', 'ECUE.CodeUE')
            ->get();

        return $etudiants;
    }
}
