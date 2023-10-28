<?php
if (App::environment('production')) {
    URL::forceScheme('https');
}
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardServiceController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\LoginController;

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


Route::post('signup', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);


Route::post('/authorisations', [CardServiceController::class, 'authorisation'])->name('authorisation');
Route::post('/settlement', [CardServiceController::class, 'settlement'])->name('settlement');
Route::post('/expired', [CardServiceController::class, 'expired'])->name('expired');

Route::group(['middleware' => ['auth:api']], function () {
    // return $request->user();

    // return url from Qpay Card


});

Route::middleware('auth:api')->group( function () {
    // Route::resource('products', ProductController::class);
});
