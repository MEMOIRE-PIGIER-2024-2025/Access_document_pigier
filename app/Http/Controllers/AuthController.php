<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\PasswordReset;
use Exception;
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


class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}', 'forgetpassword']]);
    }

    // REGISTER
    public function register(Request $request, $Matri_Elev)
    {
        try {
            $eleves = Eleve::find($Matri_Elev);
            //$eleves = Eleve::whereRaw("CAST(Matri_Elev AS CHAR) = ?", $Matri_Elev)->first();

            //$eleves = Eleve::where('Matri_Elev', $Matri_Elev)->first();
            if (!$eleves) {
                return Response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => "utilisateur inconnu",
                ], 404);
            }
            if ($eleves->active == true) {
                return Response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => "Ce compte existe déjà",
                ], 404);
            }
            $validator = Validator::make(
                $request->all(),
                [
                    'password' => 'required|string|min:6|confirmed',

                ]
            );
            if ($validator->fails()) {
                $errors = $validator->errors();
                // Traitez les erreurs de validation, par exemple, renvoyez une réponse JSON contenant les erreurs
                return response()->json(
                    [
                        'success'  => false,
                        'status' => 422,
                        'errors' => [
                            $validator->errors()
                        ],
                    ],
                    422
                );
            }
            $eleves->password = Hash::make($request->password);
            $eleves->active = true;
            $eleves->save();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Utilisateur crée',
                //'user' => $eleves,

            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'error du serveur',
            ], 500);
        }
    }






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
                    'errors' => [
                        $validator->errors()
                    ],
                ],
                422
            );
        }
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json([
                "success" => false,
                "status" => 401,
                "message" => "Compte inexistant ou mot de passe incorrecte",
            ], 401);
        }
        return $this->responseWithToken($token);
    }
    protected function responseWithToken($token)
    {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'status' => 200,
            'data' => $user,
            'access_token' => $token,
            'type' => 'Bearer',
            'expires_in' => now()->addSeconds(JWTAuth::factory()->getTTL() * 60),

        ], 200);
    }

    public function refreshToken()
    {
        if (auth()->user()) {
            return response()->json([
                'success' => true,
                'status' => 200,
                'authorisation' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'

            ]);
        }
    }

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


    public function logout()
    {
        Auth::logout();
        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Utilisateur déconnecté avec succès',
        ]);
    }


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


    public function update_user($Matri_Elev)
    {
    }
}
