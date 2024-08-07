<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\CharityAuthController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\CardServiceController;
use App\Http\Controllers\ProductFeeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\User\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// cache clear
Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 });


// user part start
Route::group(['prefix' =>'user/', 'middleware' => ['auth', 'is_user']], function(){
    Route::get('dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');
    // profile
    Route::get('profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('profile/{id}', [UserController::class, 'userProfileUpdate']);
    Route::post('changepassword', [UserController::class, 'changeUserPassword']);
    Route::put('image/{id}', [UserController::class, 'userImageUpload']);
    Route::post('profile', [UserController::class, 'updateprofile'])->name('user.update');
    // profile end


    // overdrawn
    Route::post('/update-overdrawn', [DonorController::class, 'updateUserOverdrawnAmount']);

    // donation
    Route::get('transaction-view', [TransactionController::class, 'userTransactionShow'])->name('user.transaction');
    Route::post('transaction-view', [TransactionController::class, 'userTransactionShow'])->name('user.transaction_search');
    Route::get('make-donate', [DonorController::class, 'userDonationShow'])->name('user.makedonation');
    Route::post('make-donation', [DonorController::class, 'userDonationStore'])->name('donation.store');



    // standing donation 
    Route::post('standing-donation', [DonationController::class, 'userStantingDonationStore'])->name('standing_donation.store');
    Route::post('active-standingdonation', [DonationController::class, 'activeStandinguser'])->name('user.standingstatus');
    Route::get('donation-record', [DonorController::class, 'userDonationrecod'])->name('user.donationrecord');
    Route::get('standing-order-record', [DonorController::class, 'userStandingrecod'])->name('user.standingrecord');
    Route::get('standing-order-record/{id}', [DonationController::class, 'usersingleStanding'])->name('user.singlestanding');

    //voucher
    Route::post('/addvoucher', [OrderController::class, 'storeVoucher']);

    Route::get('order-voucher-book', [OrderController::class, 'userOrderVoucherBook'])->name('user.orderbook');
    Route::get('order-history', [OrderController::class, 'userOrderview'])->name('user.orderhistory');

    // voucher controller satart 
    Route::get('process-voucher', [VoucherController::class, 'processed_Voucher_show'])->name('user.process_voucher');
    Route::post('waiting-completeBydonor', [VoucherController::class, 'waiting_CompleteBydonor']);
    Route::post('waiting-cancelBydonor', [VoucherController::class, 'waiting_CancelBydonor']);


    Route::get('my-report', [ReportController::class, 'userReport'])->name('user.report');
    Route::get('giving-report', [ReportController::class, 'userGivingReport'])->name('user.givingreport');
    Route::get('news', [HomepageController::class, 'userNews'])->name('user.news');
    Route::get('faq', [HomepageController::class, 'userfaq'])->name('user.faq');
    Route::get('contact', [ContactController::class, 'userContact'])->name('user.contact');
    Route::get('invite-friend', [HomepageController::class, 'inviteFriend'])->name('user.invitefriend');
    Route::get('settings', [HomepageController::class, 'userSettings'])->name('user.settings');

    
    Route::get('tevini-card', [HomepageController::class, 'card'])->name('user.card');


    // strip
    Route::get('stripe', [StripePaymentController::class, 'stripe']);
    Route::post('stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');


    Route::get('/topup', [DonorController::class, 'stripeDonation'])->name('stripeDonation');

    // donation calculator
    Route::post('donation-calculator', [DonationController::class, 'store'])->name('donation.calculation.store');
    Route::post('donation-calculator-update', [DonationController::class, 'DcalUpdate'])->name('donation.calculation.update');
    Route::get('donation-calculation', [DonationController::class, 'donationCal'])->name('user.donationcal');
    Route::get('donation-details/{id}', [DonationController::class, 'donationDetails'])->name('user.donationdetails');
    Route::get('on-off-donation-details', [DonationController::class, 'onOffdonationDetails'])->name('user.onOffdonationDetails');
    Route::get('active-donation-details', [DonationController::class, 'donationActive'])->name('user.donationactive');
    Route::post('one-off-donation', [DonationController::class, 'oneoffDonation'])->name('oneoffdonation');

    // other donation store
    Route::post('other-donation-store', [DonationController::class, 'otherDonationStore'])->name('donation.otherdonation');

    // charity donation link close request 
    Route::post('/close-a-link', [CharityController::class, 'closecharityLink']);
    Route::post('/transfer-to-tdf', [UserController::class, 'transferToTDF']);
    Route::post('/check-currency-amount', [UserController::class, 'checkCurrencyAmount']);

    
    Route::get('/transfer-to-tdf', [UserController::class, 'gettransferToTDF'])->name('user.transfertdf');

    // card service
    Route::get('card-service', [CardServiceController::class, 'index'])->name('userCardService');
    Route::post('cardprofile/store', [CardServiceController::class, 'cardprofilestore'])->name('cardprofile.store');

    // apply for card
    Route::get('apply-for-card', [CardServiceController::class, 'applyForCard'])->name('applyforcard');
    Route::post('apply-for-card', [CardServiceController::class, 'applyForCardstore'])->name('applyforcardstore');


    // apply for card
    Route::get('apply-for-cardholder', [CardServiceController::class, 'applyForCardHolder'])->name('applyforcardholder');
    Route::post('apply-for-cardholder', [CardServiceController::class, 'applyForCardHolderStore'])->name('applyforcardholderstore');
    Route::get('update-cardholder', [CardServiceController::class, 'updateCardHolder'])->name('cardholderUpdate');
    Route::post('update-cardholder', [CardServiceController::class, 'updateCardHolderPost'])->name('cardholderUpdatePost');

    
    // apply for card
    Route::get('order-card', [CardServiceController::class, 'orderCard'])->name('orderCard');
    Route::post('order-card', [CardServiceController::class, 'orderCardStore'])->name('orderCardstore');

    
    // card activation
    Route::get('/card-activation/{id}', [CardServiceController::class, 'cardActivation'])->name('cardActivation')->middleware('prevent-back-history');
    Route::post('/card-activation', [CardServiceController::class, 'cardActivationstore'])->name('cardActivationstore');

    // cardSetPin
    Route::get('/card-setpin/{id}', [CardServiceController::class, 'cardSetPin'])->name('cardSetPin')->middleware('prevent-back-history');
    Route::post('/card-setpin', [CardServiceController::class, 'cardSetPinstore'])->name('cardSetPinstore');
    Route::get('/mobile-verification', [OTPController::class, 'mobileVerify'])->name('mobileVerify')->middleware('prevent-back-history');
    Route::post('/mobile-verification', [OTPController::class, 'OtpConfirmation'])->name('send.sms')->middleware('prevent-back-history');
    
    Route::get('/status-verification', [OTPController::class, 'statusVerify'])->name('statusVerify')->middleware('prevent-back-history');
    Route::post('/status-verification', [OTPController::class, 'statusOtpConfirmation'])->name('status.sms')->middleware('prevent-back-history');

    Route::get('/activation-verification', [OTPController::class, 'activationVerify'])->name('activationVerify')->middleware('prevent-back-history');
    Route::post('/activation-verification', [OTPController::class, 'activationOtpConfirmation'])->name('activation.sms')->middleware('prevent-back-history');

    // cardSetPin
    Route::get('/card-status-change', [CardServiceController::class, 'cardStatusChange'])->name('cardStatusChange')->middleware('prevent-back-history');
    Route::post('/card-status-change', [CardServiceController::class, 'cardStatusChangeStore'])->name('cardStatusChangeStore');
    
    // // card service
    // Route::get('card-activation', [CardServiceController::class, 'index'])->name('cardActivation');
    // Route::post('cardprofile/store', [CardServiceController::class, 'cardprofilestore'])->name('cardActivation.store');


});
// user part end



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [App\Http\Controllers\HomepageController::class, 'index'])->name('homepage');
Route::get('/about-us', [App\Http\Controllers\AboutController::class, 'index'])->name('aboutus');
Route::get('/why-use-us', [App\Http\Controllers\AboutController::class, 'whyuseus'])->name('whyuseus');
Route::get('/team', [App\Http\Controllers\AboutController::class, 'team'])->name('team');
Route::get('/blog', [App\Http\Controllers\AboutController::class, 'blog'])->name('blog');
Route::get('/terms-&-condition', [App\Http\Controllers\AboutController::class, 'terms'])->name('terms');
Route::get('/privacy-&-policy', [App\Http\Controllers\AboutController::class, 'privacy'])->name('privacy');
Route::get('/data-declaration-right', [App\Http\Controllers\AboutController::class, 'declaration'])->name('declaration');
Route::get('/card-terms-&-condition', [App\Http\Controllers\AboutController::class, 'cardterms'])->name('cardterms');
Route::get('/how-it-works', [App\Http\Controllers\HomepageController::class, 'howitWorks'])->name('howitWorks');
Route::get('/tdf', [App\Http\Controllers\HomepageController::class, 'tdf'])->name('tdf');
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact-submit', [App\Http\Controllers\ContactController::class, 'visitorContact'])->name('contact.submit');


Route::get('make-donation', [DonorController::class, 'makeDonationAppView']);
Route::get('make-donation-success', [DonorController::class, 'makeDonationAppMessage'])->name('onlinedonation.appview');
// standing donation 
Route::post('make-online-donation', [DonorController::class, 'userOnlineDonationStore'])->name('onlinedonation.store');


// api
Route::get('/api', [App\Http\Controllers\HomepageController::class, 'apidonation'])->name('apidonation');
Route::post('/api', [App\Http\Controllers\HomepageController::class, 'apidonationCheck'])->name('apidonationchk');
Route::get('/charity_login', [App\Http\Controllers\CharityController::class, 'charity_login_show'])->name('charity_loginshow');


Route::post('/cardEnrolFingerprint', [App\Http\Controllers\HomepageController::class, 'cardEnrolFingerprint'])->name('cardEnrolFingerprint');
Route::post('/cardFingerprintDonation', [App\Http\Controllers\HomepageController::class, 'cardFingerprintDonation'])->name('cardFingerprintDonation');
Route::post('/cardIsFingerprintUserEnrolled', [App\Http\Controllers\HomepageController::class, 'cardIsFingerprintUserEnrolled'])->name('cardIsFingerprintUserEnrolled');
Route::post('/cardDeregisterFingerprint', [App\Http\Controllers\HomepageController::class, 'cardDeregisterFingerprint'])->name('cardDeregisterFingerprint');


// change password

Route::get('/change-user-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('user.chkpassword');
Route::post('/change-user-password', [App\Http\Controllers\HomeController::class, 'passwordChange'])->name('user.pwdchange');


// chatiry login
Route::post('charity_login', [CharityAuthController::class, 'login'])->name('charity.login');

// charity registration
Route::get('charity-registration', [CharityAuthController::class, 'charityRegistraion'])->name('charity.register');
Route::post('charity-registration', [CharityAuthController::class, 'charityRegistraionStore'])->name('charity.registration');

Route::post('/barcode', [OrderController::class, 'getbarCode']);
Route::post('/charity-barcode', [OrderController::class, 'getCharitybarCode']);





Route::middleware(['auth:sanctum,charity', 'verified']);
// charity part start
Route::group(['prefix' =>'charity/', 'middleware' => ['charity']], function(){
    Route::get('/dashboard', [CharityController::class, 'charityDashboard'])->name('charityDashboard');
    Route::get('/profile', [CharityController::class, 'profileShow'])->name('charity.profile');
    Route::get('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard');
    Route::post('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard_search');
    Route::post('/profile', [CharityController::class, 'updateCharity_profile'])->name('charity_profileUpdate');

    
    Route::post('/urgent-request', [CharityController::class, 'urgentRequest'])->name('charity.urgent_request');
    
    Route::get('/create-a-link', [CharityController::class, 'charityLink'])->name('charity_link');
    Route::post('/create-a-link', [CharityController::class, 'charityLinkStore']);



    
    Route::get('/process-voucher', [CharityController::class, 'processVoucher'])->name('charity.processvoucher');
    Route::post('/pvoucher-store', [CharityController::class, 'pvoucherStore'])->name('charity.pvoucher.store');
    Route::get('/process-voucher/{id}', [CharityController::class, 'instReport'])->name('charity.instreport');
    Route::post('/pvr-mail', [CharityController::class, 'instReportmail'])->name('charity.instreportmail');

    
    Route::get('/pending-voucher', [CharityController::class, 'pendingVoucher'])->name('charity.pendingvoucher');



});