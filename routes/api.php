<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VersementController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the 'api' middleware group. Make something great!
|
*/

Route::post( 'forgetpassword', 'App\Http\Controllers\AuthController@forgetPassword' );

Route::group(
    [
        'middleware' => 'api',

    ],

    function ( $router ) {
        // AUTHENTICATION
        Route::put( 'register/{Matri_Elev}', 'App\Http\Controllers\AuthController@register' );
        Route::post( 'login', 'App\Http\Controllers\AuthController@login' );
        Route::get( 'refreshtoken', 'App\Http\Controllers\AuthController@refreshToken' );
        Route::get( 'profile', 'App\Http\Controllers\AuthController@profile' );
        Route::post( 'logout', 'App\Http\Controllers\AuthController@logout' );

        // API USER
        Route::put( 'update_user/{Matri_Elev}', 'App\Http\Controllers\UserController@update_user' );

        // API VERSEMENTS
        Route::get( 'versements/{Matri_Elev}', 'App\Http\Controllers\VersementController@versements' );
        Route::get( 'all_versements/{Matri_Elev}', 'App\Http\Controllers\VersementController@all_versements' );
        Route::get( 'last_versements/{Matri_Elev}', 'App\Http\Controllers\VersementController@last_versements' );

        // API CERTIFICATS
        Route::get( 'certificats/{Matri_Elev}', 'App\Http\Controllers\CertificatController@getCertificats' );
        Route::get( 'certificat_scol/{Matri_Elev}', 'App\Http\Controllers\CertificatController@getCertificats_scol' );

        // API RELEVE DE NOTE
        Route::get( 'releves/{Matri_Elev}', 'App\Http\Controllers\RelevesController@getannee' );
        Route::get( 'releves_semestre/{Matri_Elev}/{annee_acad}', 'App\Http\Controllers\RelevesController@getreleve' );
        Route::get( 'releves_note/{Matri_Elev}/{annee_acad}/{semestre}', 'App\Http\Controllers\RelevesController@relevesnote' );
    }
);
