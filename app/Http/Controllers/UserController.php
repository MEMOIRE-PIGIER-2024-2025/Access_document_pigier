<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'register/{Matri_Elev}', 'forgetpassword']]);
    }


    public function update_user(Request $request, $Matri_Elev)
    {
        try {
            $user = Eleve::find($Matri_Elev);

            if (!$user) {
                return Response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => "utilisateur inconnu",
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
                        'message' => [
                            $validator->errors()
                        ],
                    ],
                    422
                );
            }
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Utilisateur modifié',
                //'user' => $eleves,

            ], 200);
        } catch (Exception) {
            return response()->json([
                'message' => 'error du serveur',
            ], 500);
        }
    }
}
