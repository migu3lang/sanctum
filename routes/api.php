<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', 'RegisterController@register');
Route::post('/login', 'LoginController@login');
Route::post('/logout', 'LoginController@logout')->middleware('auth:sanctum');

//institutions jorjuela 01/06/2020
Route::post('/institutions/newInstitution','institutions\InstitutionsController@newInstitution')->middleware('auth:sanctum');
Route::get('/institutions/getAllInstitutions','institutions\InstitutionsController@getAllInstitutions')->middleware('auth:sanctum');
Route::post('/institutions/getInstitution','institutions\InstitutionsController@getInstitution')->middleware('auth:sanctum');
