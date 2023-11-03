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
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CharityController;
use App\Http\Controllers\Api\DonorController;
use App\Http\Controllers\Api\OrderController;

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
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('get-all-charity', [CharityController::class, 'getAllCharity']);
    Route::post('make-donation', [DonorController::class, 'userDonationStore']);

    
    Route::get('donation-record', [DonorController::class, 'userDonationrecod']);
    Route::get('standing-order-record', [DonorController::class, 'userStandingrecod']);

    Route::get('order-history', [OrderController::class, 'userOrderview']);


});

Route::middleware('auth:api')->group( function () {
    // Route::resource('products', ProductController::class);
});
