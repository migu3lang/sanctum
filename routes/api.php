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

Route::get('/pacho', function() {
    dd('prueba');
})->middleware('auth:sanctum');


Route::post('/register', 'RegisterController@register');
Route::post('/login', 'LoginController@login');
Route::post('/logout', 'LoginController@logout')->middleware('auth:sanctum');

//institutions jorjuela 01/06/2020
Route::group(['prefix'=>'institutions','namespace'=>'Institutions','middleware'=>'auth:sanctum'],function(){
    Route::post('/newInstitution','InstitutionsController@newInstitution');
    Route::get('/getAllInstitutions','InstitutionsController@getAllInstitutions');
    Route::post('/getInstitution','InstitutionsController@getInstitution');
    Route::post('/editInstitution','InstitutionsController@editInstitution');
    Route::post('/deleteInstitution','InstitutionsController@deleteInstitution');
    Route::post('/deleteMultipleInstitutions','InstitutionsController@deleteMultipleInstitutions');
});


// clients


//php artisan cache: clear
//php artisan config: clear
//php artisan cache: clear

Route::group(['prefix'=>'clients','namespace'=>'Administracion','middleware'=>'auth:sanctum'],function(){
Route::get('/list','AdminclienteController@index');
Route::post('/store','AdminclienteController@store');
});

