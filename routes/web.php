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
    Route::get('make-donation', [DonorController::class, 'userDonationShow'])->name('user.makedonation');
    Route::post('make-donation', [DonorController::class, 'userDonationStore'])->name('donation.store');
// standing donation 
    Route::post('standing-donation', [DonationController::class, 'userStantingDonationStore'])->name('standing_donation.store');
    Route::post('active-standingdonation', [DonationController::class, 'activeStandinguser'])->name('user.standingstatus');
    Route::get('donation-record', [DonorController::class, 'userDonationrecod'])->name('user.donationrecord');
    Route::get('standing-order-record', [DonorController::class, 'userStandingrecod'])->name('user.standingrecord');

    //voucher
    Route::post('/addvoucher', [OrderController::class, 'storeVoucher'])->name('voucher.store');

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
Route::get('/how-it-works', [App\Http\Controllers\HomepageController::class, 'howitWorks'])->name('howitWorks');
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact-submit', [App\Http\Controllers\ContactController::class, 'visitorContact'])->name('contact.submit');

// api
Route::get('/api', [App\Http\Controllers\HomepageController::class, 'apidonation'])->name('apidonation');
Route::post('/api', [App\Http\Controllers\HomepageController::class, 'apidonationCheck'])->name('apidonationchk');
Route::get('/charity_login', [App\Http\Controllers\CharityController::class, 'charity_login_show'])->name('charity_loginshow');


// chatiry login
Route::post('charity_login', [CharityAuthController::class, 'login'])->name('charity.login');

// charity registration
Route::get('charity-registration', [CharityAuthController::class, 'charityRegistraion'])->name('charity.register');
Route::post('charity-registration', [CharityAuthController::class, 'charityRegistraionStore'])->name('charity.registration');


Route::middleware(['auth:sanctum,charity', 'verified']);
// charity part start
Route::group(['prefix' =>'charity/', 'middleware' => ['charity']], function(){
    Route::get('/dashboard', [CharityController::class, 'charityDashboard'])->name('charityDashboard');
    Route::get('/profile', [CharityController::class, 'profileShow'])->name('charity.profile');
    Route::get('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard');
    Route::post('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard_search');
    Route::post('/profile', [CharityController::class, 'updateCharity_profile'])->name('charity_profileUpdate');
});