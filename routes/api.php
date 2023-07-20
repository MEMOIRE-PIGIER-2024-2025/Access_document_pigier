<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(
    [
        'middleware' => 'api',

    ],
    function ($router) {
        Route::put('register/{Matri_Elev}', 'App\Http\Controllers\AuthController@register');
        Route::post('login', 'App\Http\Controllers\AuthController@login');
        Route::get('refreshtoken', 'App\Http\Controllers\AuthController@refreshToken');
        Route::get('profile', 'App\Http\Controllers\AuthController@profile');
        Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    }
);
