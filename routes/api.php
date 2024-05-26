<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/user', function(){
//     return "Hello Wolrd";
// });
// Route::post('/user', function(){
//     return response()->json();
// });

// Route::get('test', function(){
//      p("Working");
// });

Route::post('user/store','App\Http\Controllers\Api\ApiController@store');

Route::get('user/get/{flag}','App\Http\Controllers\Api\ApiController@index');

Route::delete('user/delete/{id}', 'App\Http\Controllers\Api\ApiController@destroy');

Route::put('user/update/{id}', 'App\Http\Controllers\Api\ApiController@update');

 Route::patch('change-password/{id}', 'App\Http\Controllers\Api\ApiController@edit');

// Route::patch('user/change-password/{id}', [ApiController::class,'edit']);