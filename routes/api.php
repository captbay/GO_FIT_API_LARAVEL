<?php

use App\Http\Controllers\authController;
use App\Http\Controllers\memberController;
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


//user 
Route::post('users/login', [authController::class, 'login']);
Route::post('users/register', [authController::class, 'register']);

Route::post('member/store', [memberController::class, 'store']);
Route::post('member/aktivasi/{id}', [memberController::class, 'aktivasi']);


//print pdf
Route::get('member/generatePdf/{id}', [memberController::class, 'generateMemberCard']);

Route::group(['middleware' => 'auth:api'], function () {
    // user
    Route::apiResource(
        '/users',
        \App\Http\Controllers\UserController::class
    );
    Route::post('users/update/{id}', [UserController::class, 'update']);
    Route::post('users/logout', [UserController::class, 'logout']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });