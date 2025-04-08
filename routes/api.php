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
use App\Http\Controllers\Api\FundTransferController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TDFTransactionController;
use App\Http\Controllers\StripePaymentController;

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

Route::get('/address-finder-api', [DashboardController::class, 'addressFinderApi']);



Route::group(['middleware' => ['auth:api']], function () {
    // return $request->user();
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('userbalance', [DashboardController::class, 'userBalance']);
    Route::post('account-delete-request', [DashboardController::class, 'accountDeleteRequest']);
    Route::get('get-user-details', [RegisterController::class, 'userDetails']);
    Route::get('get-all-charity', [CharityController::class, 'getAllCharity']);
    
    Route::get('make-donation', [DonorController::class, 'getMakeDonation']);
    Route::get('make-donation-success', [DonorController::class, 'makeDonationAppMessage']);
    Route::post('make-donation', [DonorController::class, 'userDonationStore']);

    Route::post('profile', [UserController::class, 'updateprofile']);
    Route::post('profile-image', [UserController::class, 'updateprofileImage']);

    
    // charity donation link close request 
    Route::post('/close-a-link', [CharityController::class, 'closecharityLink']);

    
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
    Route::post('/book-add-to-cart', [VoucherBookController::class, 'userOrderVoucherBookstoreCart']);
    Route::get('voucher-order-edit/{id}', [VoucherBookController::class, 'voucherEditByDonor']);
    Route::post('voucher-order-update', [VoucherBookController::class, 'voucherUpdateByDonor']);

    
    Route::post('waiting-completeBydonor', [VoucherBookController::class, 'waiting_CompleteBydonor']);
    Route::post('waiting-cancelBydonor', [VoucherBookController::class, 'waiting_CancelBydonor']);

    
    Route::get('donation-calculation', [MaaserController::class, 'donationCal']);
    Route::post('one-off-donation', [MaaserController::class, 'oneoffDonation']);
    Route::post('other-donation-store', [MaaserController::class, 'otherDonationStore']);
    Route::get('other-donation', [MaaserController::class, 'getOtherDonation']);

    
    Route::get('on-off-donation-details', [MaaserController::class, 'onOffdonationDetails']);

     // regular income
     Route::post('donation-calculator', [MaaserController::class, 'store']);
     Route::post('donation-calculator-update', [MaaserController::class, 'DcalUpdate']);

     
    Route::post('active-donation-details', [MaaserController::class, 'donationActive']);
    Route::get('donation-details/{id}', [MaaserController::class, 'donationDetails']);
    Route::post('/contact-submit', [ContactController::class, 'visitorContact']);

    // tdf transaction
    Route::get('/tdf-transaction', [TDFTransactionController::class, 'getTDFtransaction']);
    Route::post('/transfer-to-tdf', [TDFTransactionController::class, 'transferToTDF']);
    

    
    Route::post('stripe-top-up', [StripePaymentController::class, 'stripetopUpPost']);

    // fund transfer api start
    Route::get('fund-transfer', [FundTransferController::class, 'balanceTransfer']);
    Route::post('fund-transfer', [FundTransferController::class, 'balanceTransferStore']);
    // fund transfer api end

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
