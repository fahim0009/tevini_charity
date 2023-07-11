<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardServiceController;

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

Route::post('/authorisations', [CardServiceController::class, 'pruchaseStore'])->name('purchaseStore');

Route::group(['middleware' => ['auth:api']], function () {
    // return $request->user();

    // return url from Qpay Card


});
