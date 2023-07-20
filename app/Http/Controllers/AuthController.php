<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}']]);
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
                'message' => 'Utilisateur modifier avec succès',
                'user' => $eleves,

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
                "error" => "Compte inexistant ou mot de passe incorrecte",
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
            // 'data' => $user,
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
}
