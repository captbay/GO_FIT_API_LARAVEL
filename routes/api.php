<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\class_detailController;
use App\Http\Controllers\class_runningController;
use App\Http\Controllers\instrukturController;
use App\Http\Controllers\memberController;
use App\Http\Controllers\pegawaiController;
use App\Http\Controllers\promo_cashController;
use App\Http\Controllers\promo_classController;
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


//auth
Route::post('users/login', [authController::class, 'login']);
Route::post('users/store/admin', [authController::class, 'registerAdmin']);

//harus login baru bisa akses
Route::group(['middleware' => 'auth:api'], function () {
    //auth extention after login baru bisa akses
    Route::post('users/updatePassword', [authController::class, 'updatePassword']);
    Route::post('users/resetPassword', [authController::class, 'resetPassword']);
    Route::post('users/logout', [authController::class, 'logout']);

    //member
    Route::apiResource(
        'member',
        memberController::class
    );
    Route::get('member/generatePdf/{id}', [memberController::class, 'generateMemberCard']);

    //instruktur
    Route::apiResource(
        'instruktur',
        instrukturController::class
    );

    //pegawai
    Route::apiResource(
        'pegawai',
        pegawaiController::class
    );

    //class_detail
    Route::apiResource(
        'class_detail',
        class_detailController::class
    );

    //class_running
    Route::apiResource(
        'class_running',
        class_runningController::class
    );

    //promo_cash
    Route::apiResource(
        'promo_cash',
        promo_cashController::class
    );

    //promo_class
    Route::apiResource(
        'promo_class',
        promo_classController::class
    );
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });