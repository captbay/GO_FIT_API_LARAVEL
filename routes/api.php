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


//user 
Route::post('users/login', [UserController::class, 'login']);
Route::post('users/register', [UserController::class, 'register']);

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->name('verification.verify');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
});

Route::group(['middleware' => 'auth:api'], function () {
    // user
    Route::apiResource(
        '/users',
        \App\Http\Controllers\UserController::class
    );
    Route::post('users/update/{id}', [UserController::class, 'update']);
    Route::post('users/logout', [UserController::class, 'logout']);

    //pembelian pesulap
    Route::apiResource(
        '/pembelianpesulaps',
        \App\Http\Controllers\PembelianPesulapController::class
    );
    Route::post('pembelianpesulaps/store/{id_user}/{id_product}', [PembelianPesulapController::class, 'store']);

    //pembelian komika
    Route::apiResource(
        '/pembeliankomikas',
        \App\Http\Controllers\PembelianKomikaController::class
    );
    Route::post('pembeliankomikas/store/{id_user}/{id_product}', [PembelianKomikaController::class, 'store']);

    //pembelian band
    Route::apiResource(
        '/pembelianbands',
        \App\Http\Controllers\PembelianBandController::class
    );
    Route::post('pembelianbands/store/{id_user}/{id_product}', [PembelianBandController::class, 'store']);

    // band
    Route::apiResource(
        '/bands',
        \App\Http\Controllers\BandController::class
    );

    Route::post('bands/update/{id}', [BandController::class, 'update']);

    // pesulap
    Route::apiResource(
        '/pesulaps',
        \App\Http\Controllers\PesulapController::class
    );
    Route::post('pesulaps/update/{id}', [PesulapController::class, 'update']);

    //komika
    Route::apiResource(
        '/komikas',
        \App\Http\Controllers\KomikaController::class
    );
    Route::post('komikas/update/{id}', [KomikaController::class, 'update']);

    //pembayaran
    Route::apiResource(
        '/pembayarans',
        \App\Http\Controllers\PembayaranController::class
    );
    Route::post('pembayarans/store/{id}', [PembayaranController::class, 'store']);
    Route::post('pembayarans/deleteAll', [PembayaranController::class, 'deleteAll']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});