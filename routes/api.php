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
use App\Http\Controllers\Api\UserController;

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
Route::post('charity_login', [RegisterController::class, 'charity_login']);


Route::post('/authorisations', [CardServiceController::class, 'authorisation']);
Route::post('/settlement', [CardServiceController::class, 'settlement']);
Route::post('/expired', [CardServiceController::class, 'expired']);



Route::group(['middleware' => ['auth:api']], function () {
    // return $request->user();
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('get-user-details', [RegisterController::class, 'userDetails']);
    Route::get('get-all-charity', [CharityController::class, 'getAllCharity']);
    Route::post('make-donation', [DonorController::class, 'userDonationStore']);
    Route::post('profile', [UserController::class, 'updateprofile']);
    Route::post('profile-image', [UserController::class, 'updateprofileImage']);

    
    Route::get('donation-record', [DonorController::class, 'userDonationrecod']);
    Route::get('standing-order-record', [DonorController::class, 'userStandingrecod']);
    Route::get('standing-order-record/{id}', [DonorController::class, 'usersingleStanding']);
    
    Route::post('active-standingdonation', [DonorController::class, 'activeStandinguser']);


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

    
    Route::get('on-off-donation-details', [MaaserController::class, 'onOffdonationDetails']);

     // regular income
     Route::post('donation-calculator', [MaaserController::class, 'store']);
     Route::post('donation-calculator-update', [MaaserController::class, 'DcalUpdate']);

     
    Route::post('active-donation-details', [MaaserController::class, 'donationActive']);
    Route::get('donation-details/{id}', [MaaserController::class, 'donationDetails']);
    Route::post('/contact-submit', [ContactController::class, 'visitorContact']);

    

});




Route::get('all-donor', [DashboardController::class, 'get_all_donor']);
Route::get('charity-dashboard/{id}', [DashboardController::class, 'charity_dashboard']);
Route::get('/charity-profile/{id}', [CharityController::class, 'profileShow']);

Route::get('/charity-transaction/{id}', [CharityController::class, 'charityTransaction']);



Route::post('/charity-profile-update', [CharityController::class, 'updateCharity_profile']);
Route::post('/create-a-link', [CharityController::class, 'charityLinkStore']);
Route::post('/urgent-request', [CharityController::class, 'urgentRequest']);

Route::middleware(['auth:sanctum,charity', 'verified']);
// charity part start
Route::group(['middleware' => ['charity']], function(){
    



    // Route::get('/profile', [CharityController::class, 'profileShow'])->name('charity.profile');
    // Route::get('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard');
    // Route::post('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard_search');
    // Route::post('/profile', [CharityController::class, 'updateCharity_profile'])->name('charity_profileUpdate');

    
    // Route::post('/urgent-request', [CharityController::class, 'urgentRequest'])->name('charity.urgent_request');
    
    // Route::get('/create-a-link', [CharityController::class, 'charityLink'])->name('charity_link');
    // Route::post('/create-a-link', [CharityController::class, 'charityLinkStore']);
});
