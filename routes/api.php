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
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\StandingDonationController;
use App\Http\Controllers\Api\VoucherBookController;
use App\Http\Controllers\Api\MaaserController;
use App\Http\Controllers\Api\ContactController;

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


Route::post('/authorisations', [CardServiceController::class, 'authorisation']);
Route::post('/settlement', [CardServiceController::class, 'settlement']);
Route::post('/expired', [CardServiceController::class, 'expired']);

Route::group(['middleware' => ['auth:api']], function () {
    // return $request->user();
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('get-all-charity', [CharityController::class, 'getAllCharity']);
    Route::post('make-donation', [DonorController::class, 'userDonationStore']);

    
    Route::get('donation-record', [DonorController::class, 'userDonationrecod']);
    Route::get('standing-order-record', [DonorController::class, 'userStandingrecod']);

    Route::get('order-history', [OrderController::class, 'userOrderview']);

    // waiting voucher 
    Route::get('process-voucher', [VoucherController::class, 'processed_Voucher_show']);
    Route::get('transaction-view', [TransactionController::class, 'userTransactionShow']);

    // standing donation 
    Route::post('standing-donation', [StandingDonationController::class, 'standingDonationStore']);

    
    Route::get('order-voucher-book', [VoucherBookController::class, 'userOrderVoucherBook']);
    Route::post('/voucher-store', [VoucherBookController::class, 'storeVoucher']);

    
    Route::get('donation-calculation', [MaaserController::class, 'donationCal']);
    Route::post('one-off-donation', [MaaserController::class, 'oneoffDonation']);
    Route::post('other-donation-store', [MaaserController::class, 'otherDonationStore']);

     // regular income
     Route::post('donation-calculator', [MaaserController::class, 'store']);
     Route::post('donation-calculator-update', [MaaserController::class, 'DcalUpdate']);

     
    Route::post('/contact-submit', [ContactController::class, 'visitorContact']);

});

Route::middleware('auth:api')->group( function () {
    // Route::resource('products', ProductController::class);
});
