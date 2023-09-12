<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Etudiant;
use App\Models\Historique;
use App\Models\PasswordReset;
use Exception;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\TryCatch;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}', 'forgetpassword']]);
    }

    /********************************* REGISTER ******************************/
    // Verification de letudiant dans la table etudiants
    public function register(Request $request, $Matri_Elev)
    {
        try {
            $etud = Etudiant::where('Matri_Elev', $Matri_Elev)->first();
            if (!$etud) {
                //echo "vide";
                return $this->registerEtudiant($Matri_Elev, $request);
            } else {
                if ($etud->active == false) {
                    return $this->updateEtudiants($etud, $request);
                } else {
                    return Response()->json([
                        'status' => 401,
                        'message' => "Cet élève existe déjà, veuillez-vous connectez !",
                    ]);
                }
            }
        } catch (Exception $e) {

            return response()->json([
                //'message' => 'error du serveur',
                'messages' => 'Erreur du serveur',
                'error' => $e->getMessage(),
            ]);
        }
    }
    //migration des eleves de la table eleves et historique dans la table etudiants
    protected function registerEtudiant($Matri_Elev, $request)
    {
        // Verification de leleve dans la table eleves
        $eleves = Eleve::find($Matri_Elev);
        if (!$eleves) {
            // Élève non trouvé dans la table "Eleve"

            $historique = Historique::where('Matri_Elev', $Matri_Elev)
                ->latest('ID_Historique')
                ->take(1)
                ->get();
            if ($historique->isEmpty()) {
                // Historique vide
                return Response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => "l'élève  ayant ce matricule est introuvable.",
                ]);
            } else {
                // Historique trouvé dans la table "Historique"
                $historiqueData = $historique->first();
                return $this->insertionHistoriqueEtudiant($historiqueData, $request);
            }
        } else {
            // Élève trouvé dans la table "Eleve"
            return $this->insertionEleveEtudiant($eleves, $request);
        }
    }

    protected function insertionHistoriqueEtudiant($historiqueData, $request)
    {
        $etudiants = Etudiant::create([
            'Matri_Elev' => $historiqueData->Matri_Elev,
            'Nom_Elev' => $historiqueData->Nom_Elev,
            'Sexe_Elev' => $historiqueData->Sexe_Elev,
            'Code_Detcla' => $historiqueData->Code_Detcla,
            'Nom_Cla' => $historiqueData->Nom_Cla,
            'Code_Nat' => $historiqueData->Code_Nat,
            'Datenais_Elev' => $historiqueData->Datenais_Elev,
            'Actenais_Elev' => $historiqueData->Actenais_Elev,
            'AnneeSco_Orig' => $historiqueData->AnneeSco_Orig,
            'DateEntre_Elev' => $historiqueData->DateEntre_Elev,
            'Code_Niv' => $historiqueData->Code_Niv,
            'AnneeSco_Elev' => $historiqueData->AnneeSco_Elev,
            'MontantSco_Elev' => $historiqueData->MontantSco_Elev,
            'SoldSco_Elev' => $historiqueData->SoldSco_Elev,
            'SoldFraisExam' => $historiqueData->SoldFraisExam,
            'mailetud' => $historiqueData->mailetud,
            'natPiece' => $historiqueData->natPiece,
            'montantExamen' => $historiqueData->montantExamen,
        ]);
        if ($etudiants) {
            // Insertion réussie
            return $this->updateEtudiants($historiqueData, $request);
        }
    }

    protected function insertionEleveEtudiant($eleves, $request)
    {
        $datenais = Carbon::parse($eleves->Datenais_Elev)->toDateString();
        $etudiants = Etudiant::create([
            'Matri_Elev' => $eleves->Matri_Elev,
            'Nom_Elev' => $eleves->Nom_Elev,
            'Sexe_Elev' => $eleves->Sexe_Elev,
            'Code_Detcla' => $eleves->Code_Detcla,
            'Nom_Cla' => $eleves->Nom_Cla,
            'Code_Nat' => $eleves->Code_Nat,
            'Datenais_Elev' => $datenais,
            'Actenais_Elev' => $eleves->Actenais_Elev,
            'AnneeSco_Orig' => $eleves->AnneeSco_Orig,
            'DateEntre_Elev' => $eleves->DateEntre_Elev,
            'Code_Niv' => $eleves->Code_Niv,
            'AnneeSco_Elev' => $eleves->AnneeSco_Elev,
            'MontantSco_Elev' => $eleves->MontantSco_Elev,
            'SoldSco_Elev' => $eleves->SoldSco_Elev,
            'SoldFraisExam' => $eleves->SoldFraisExam,
            'mailetud' => $eleves->mailetud,
            'natPiece' => $eleves->natPiece,
            'montantExamen' => $eleves->montantExamen,
        ]);
        if ($etudiants) {
            // Insertion réussie
            return $this->updateEtudiants($eleves, $request);
        }
    }

    protected function updateEtudiants($Data, $request)
    {
        $etudiantId = $Data->Matri_Elev;
        $etudiant = Etudiant::where('Matri_Elev', $etudiantId)->first();
        if (!$etudiant) {
            return Response()->json([
                'success' => false,
                'status' => 404,
                'message' => "élève introuvable",
            ]);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'password' => 'required|string|min:6|confirmed',
                'mailetud' => 'required|email|unique:etudiants',

            ],
        );
        if ($validator->fails()) {
            // Traitez les erreurs de validation, par exemple, renvoyez une réponse JSON contenant les erreurs
            return response()->json(
                [
                    'success'  => false,
                    'status' => 422,
                    'message' => $validator->errors(),
                ]
            );
        }
        $etudiant->password = Hash::make($request->password);
        $etudiant->mailetud = $request->mailetud;
        $etudiant->active = true;
        $etudiant->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'élève créé',
            //'user' => $eleves,

        ]);
    }

    /********************************* LOGIN ******************************/
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'Matri_Elev' => 'required|string',
                'password' => 'required',

            ]
        );
        if ($validator->fails()) {
            $errors = $validator->errors();
            // Traitez les erreurs de validation, par exemple, renvoyez une réponse JSON contenant les erreurs
            return response()->json(
                [
                    'success'  => false,
                    'status' => 422,
                    'message' => $validator->errors(),

                ]
                
            );
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json([
                "success" => false,
                "status" => 401,
                "message" => "Compte inexistant ou mot de passe incorrecte",
            ]);
        }
        return $this->responseWithToken($token);
    }
    protected function responseWithToken($token)
    {
        $date = now()->addSeconds(JWTAuth::factory()->getTTL() * 60);
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => $user,
            'access_token' => $token,
            'type' => 'Bearer',
            'expires_in' => date($date),
            'date_expire_in' => $date->format('Y-m-d'),
            'heure_expire_in' => $date->format('H:i:s'),


        ]);
    }

    /********************************* REFRESHTOKEN ******************************/

    public function refreshToken()
    {
        $date = now()->addSeconds(JWTAuth::factory()->getTTL() * 60);

        if (auth()->user()) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'authorisation' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                    //'expires_in' => now()->addSeconds(JWTAuth::factory()->getTTL() * 60),
                    'expires_in' => date($date),


                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'

            ]);
        }
    }
    /********************************* PROFILE USER ******************************/

    public function profile()
    {
        if (auth()->user()) {
            return response()->json(auth()->user());
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'

            ]);
        }
    }

    /********************************* LOGOUT ******************************/

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Utilisateur déconnecté avec succès',
        ]);
    }

    /********************************* FORGETPASSWORD ******************************/

    public function forgetPassword(Request $request)
    {
        try {
            $eleves = Eleve::where('mailetud', $request->mailetud)->first();
            if ($eleves) {
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/reset-password?token=' . $token;

                $data['url'] = $url;
                $data['mailetud'] = $request->mailetud;
                $data['title'] = "Réinitialisation Mot de Passe";
                $data['body'] = "Veuillez cliquer sur ce lien pour réinitialiser votre mot de passe";
                Mail::send('forgetPasswordMail', ['data' => $data], function ($message) use ($data) {
                    $message->to($data['mailetud'])->subject($data['title']);
                });
                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                PasswordReset::updateOrCreate(
                    ['email' => $request->mailetud],
                    [
                        'email' => $request->mailetud,
                        'token' => $token,
                        'created_at' => $request->$datetime
                    ]
                );
                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Veuillez vérifier votre mail',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => 401,
                    'message' => 'Ce mail ne correspond pas à celui fournit lors de votre inscription',
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),

            ]);
        }
    }
}
