<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\auth\VerifyController;
use App\Http\Controllers\KitController;
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

Route::get('/', function (Request $request) {
    return response()->json(['message' => 'Welcome to DemoKit API'], 200);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('verify', [VerifyController::class, 'confirmUser']);
Route::post('resend', [VerifyController::class, 'resendConfToken']);



Route::group(['middleware'=> ['aws-cognito',]], function(){


    //Kits Endpoints
    Route::get('kits', [KitController::class, 'allKits']);
    Route::get('kits/{id}', [KitController::class, 'getKit']);
    Route::post('kits', [KitController::class, 'create']);
    Route::post('kits/{id}/info', [KitController::class, 'basicInfo']);
    Route::post('kits/{id}/metadata', [KitController::class, 'addMetadata']);
    Route::post('kits/{id}/content', [KitController::class, 'addContent']);

    Route::post('reset', [AuthController::class, 'resetPassword']);

Route::post('user', function(){
    return response()->json(['message' => 'Token still valid'], 200);;
});

});

Route::fallback(function (){
    return response()->json(['message' => 'Endpoint not Found'], 404);
});
