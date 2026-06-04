<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\FrontendController;
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
use App\Http\Controllers\BalanceTransferController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Auth\CharityPasswordResetController;
use App\Http\Controllers\User\GiftAidController;

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

// Cache clear
Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});

Route::get('/queue-work', function () {
    Artisan::call('queue:work');
    return "done!";
});

// Use mobile app
Route::get('app-version', [AboutController::class, 'appVersion']);

// Email Verification
Route::post('/email/resend-verification', function (Request $request) {
    $user = Auth::user();
    if ($user->hasVerifiedEmail()) {
        return redirect()->back()->with('message', 'Your email is already verified.');
    }
    $user->sendEmailVerificationNotification();
    return redirect()->back()->with('message', 'Please check your email. we have send a link to confirm your email!');
})->middleware(['auth'])->name('verification.resend');

Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verification'])->name('verification.verify');
Route::get('/verify-email/{token}', [UserController::class, 'verifyEmail'])->name('verify.email');

// Charity Password Reset
Route::get('charity/forgot-password', [CharityPasswordResetController::class, 'showLinkRequestForm'])->name('charity.password.request');
Route::post('charity/forgot-password', [CharityPasswordResetController::class, 'sendResetLinkEmail'])->name('charity.password.email');
Route::get('charity/reset-password/{token}', [CharityPasswordResetController::class, 'showResetForm'])->name('charity.password.reset');
Route::post('charity/reset-password', [CharityPasswordResetController::class, 'reset'])->name('charity.password.update');

// Authentication
Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Public Pages
Route::get('/', [HomepageController::class, 'index'])->name('homepage');
Route::get('/about-us', [AboutController::class, 'index'])->name('aboutus');
Route::get('/why-use-us', [AboutController::class, 'whyuseus'])->name('whyuseus');
Route::get('/team', [AboutController::class, 'team'])->name('team');
Route::get('/blog', [AboutController::class, 'blog'])->name('blog');
Route::get('/terms-&-condition', [AboutController::class, 'terms'])->name('terms');
Route::get('/privacy-&-policy', [AboutController::class, 'privacy'])->name('privacy');
Route::get('/data-declaration-right', [AboutController::class, 'declaration'])->name('declaration');
Route::get('/card-terms-&-condition', [AboutController::class, 'cardterms'])->name('cardterms');
Route::get('/how-it-works', [HomepageController::class, 'howitWorks'])->name('howitWorks');
Route::get('/tdf', [HomepageController::class, 'tdf'])->name('tdf');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact-submit', [ContactController::class, 'visitorContact'])->name('contact.submit');

// new pages
Route::get('/v2/home', [HomepageController::class, 'indexV2'])->name('homepageV2');
Route::get('/online-donation', [FrontendController::class, 'onlineDonation'])->name('onlineDonation');
Route::post('/online-donation', [FrontendController::class, 'onlineDonationStore'])->name('front.onlinedonation.store');

Route::get('/online-voucher-book', [FrontendController::class, 'orderVoucherBooks'])->name('orderVoucherBooks');

// App Safari View Controller (for testing)
Route::get('make-donation', [DonorController::class, 'makeDonationAppView']);
Route::get('make-donation-success', [DonorController::class, 'makeDonationAppMessage'])->name('onlinedonation.appview');
Route::post('make-online-donation', [DonorController::class, 'userOnlineDonationStore'])->name('onlinedonation.store');

// API for Campaign
Route::get('/api', [HomepageController::class, 'apidonation'])->name('apidonation');
Route::post('/api', [HomepageController::class, 'apidonationCheck'])->name('apidonationchk');

// Card Fingerprint
Route::post('/cardEnrolFingerprint', [HomepageController::class, 'cardEnrolFingerprint'])->name('cardEnrolFingerprint');
Route::post('/cardFingerprintDonation', [HomepageController::class, 'cardFingerprintDonation'])->name('cardFingerprintDonation');
Route::post('/cardIsFingerprintUserEnrolled', [HomepageController::class, 'cardIsFingerprintUserEnrolled'])->name('cardIsFingerprintUserEnrolled');
Route::post('/cardDeregisterFingerprint', [HomepageController::class, 'cardDeregisterFingerprint'])->name('cardDeregisterFingerprint');

// Change Password
Route::get('/change-user-password', [HomeController::class, 'changePassword'])->name('user.chkpassword');
Route::post('/change-user-password', [HomeController::class, 'passwordChange'])->name('user.pwdchange');

// Charity Login
Route::get('/charity_login', [CharityController::class, 'charity_login_show'])->name('charity_loginshow');
Route::post('charity_login', [CharityAuthController::class, 'login'])->name('charity.login');

// Charity Registration
Route::get('charity-registration', [CharityAuthController::class, 'charityRegistraion'])->name('charity.register');
Route::post('charity-registration', [CharityAuthController::class, 'charityRegistraionStore'])->name('charity.registration');

// Barcode
Route::post('/barcode', [OrderController::class, 'getbarCode']);
Route::post('/charity-barcode', [OrderController::class, 'getCharitybarCode']);