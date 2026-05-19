<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\CardServiceController;
use App\Http\Controllers\BalanceTransferController;
use App\Http\Controllers\User\GiftAidController;
use App\Http\Controllers\User\UserController;

/*
|--------------------------------------------------------------------------
| User/Donor Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'user/', 'middleware' => ['auth', 'is_user']], function () {

    // Dashboard
    Route::get('dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');

    // ==================== Profile ====================
    Route::get('profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('profile/{id}', [UserController::class, 'userProfileUpdate']);
    Route::post('changepassword', [UserController::class, 'changeUserPassword']);
    Route::put('image/{id}', [UserController::class, 'userImageUpload']);
    Route::post('profile', [UserController::class, 'updateprofile'])->name('user.update');
    Route::post('profile-delete-request', [UserController::class, 'profileDeleteRequest'])->name('account.deleteRequest');
    Route::post('new-email-account', [UserController::class, 'emailAccountStore'])->name('emailAccountStore');
    Route::post('email-account-update', [UserController::class, 'emailAccountUpdate'])->name('emailAccountUpdate');
    Route::delete('user-email/delete/{id}', [UserController::class, 'destroy'])->name('useremailDestroy');

    // ==================== GiftAid ====================
    Route::get('current-year-giftaid-transaction/{type?}', [GiftAidController::class, 'currentYearTransaction'])->name('user.currentyear.giftaidtran');

    // ==================== Overdrawn ====================
    Route::post('/update-overdrawn', [DonorController::class, 'updateUserOverdrawnAmount']);

    // ==================== Transactions ====================
    Route::get('transaction-view', [TransactionController::class, 'userTransactionShow'])->name('user.transaction');
    Route::get('donor-transaction-view', [TransactionController::class, 'donorTransactionShow'])->name('user.donor.alltransaction');
    Route::post('transaction-view', [TransactionController::class, 'userTransactionShow'])->name('user.transaction_search');

    // ==================== Donations ====================
    Route::get('make-donate', [DonorController::class, 'userDonationShow'])->name('user.makedonation');
    Route::post('make-donation', [DonorController::class, 'userDonationStore'])->name('donation.store');

    // Standing Donation
    Route::post('standing-donation', [DonationController::class, 'userStantingDonationStore'])->name('standing_donation.store');
    Route::post('active-standingdonation', [DonationController::class, 'activeStandinguser'])->name('user.standingstatus');
    Route::get('donation-record', [DonorController::class, 'userDonationrecod'])->name('user.donationrecord');
    Route::get('standing-order-record', [DonorController::class, 'userStandingrecod'])->name('user.standingrecord');
    Route::get('standing-order-record/{id}', [DonationController::class, 'usersingleStanding'])->name('user.singlestanding');

    // Donation Calculator
    Route::post('donation-calculator', [DonationController::class, 'store'])->name('donation.calculation.store');
    Route::post('donation-calculator-update', [DonationController::class, 'DcalUpdate'])->name('donation.calculation.update');
    Route::get('donation-calculation', [DonationController::class, 'donationCal'])->name('user.donationcal');
    Route::get('donation-details/{id}', [DonationController::class, 'donationDetails'])->name('user.donationdetails');
    Route::get('on-off-donation-details', [DonationController::class, 'onOffdonationDetails'])->name('user.onOffdonationDetails');
    Route::get('active-donation-details', [DonationController::class, 'donationActive'])->name('user.donationactive');
    Route::post('one-off-donation', [DonationController::class, 'oneoffDonation'])->name('oneoffdonation');

    // Other Donation
    Route::get('other-donation-details', [DonationController::class, 'otherdonationDetails'])->name('user.otherdonationDetails');
    Route::post('other-donation-store', [DonationController::class, 'otherDonationStore'])->name('donation.otherdonation');

    // ==================== Voucher ====================
    Route::post('/addvoucher', [OrderController::class, 'storeVoucher']);
    Route::get('order-voucher-book', [OrderController::class, 'userOrderVoucherBook'])->name('user.orderbook');
    Route::post('order-voucher-book/cart/store', [OrderController::class, 'userOrderVoucherBookstoreCart'])->name('orderbook.cart.store');
    Route::get('order-history', [OrderController::class, 'userOrderview'])->name('user.orderhistory');
    Route::get('voucher-order-edit/{id}', [OrderController::class, 'voucherEditByDonor'])->name('voucherBookEdit');
    Route::post('donor-voucher-update', [OrderController::class, 'updateVoucher']);
    Route::get('process-voucher', [VoucherController::class, 'processed_Voucher_show'])->name('user.process_voucher');
    Route::post('waiting-completeBydonor', [VoucherController::class, 'waiting_CompleteBydonor']);
    Route::post('waiting-cancelBydonor', [VoucherController::class, 'waiting_CancelBydonor']);

    // ==================== Reports ====================
    Route::get('my-report', [ReportController::class, 'userReport'])->name('user.report');
    Route::get('giving-report', [ReportController::class, 'userGivingReport'])->name('user.givingreport');

    // ==================== Pages ====================
    Route::get('news', [App\Http\Controllers\HomepageController::class, 'userNews'])->name('user.news');
    Route::get('faq', [App\Http\Controllers\HomepageController::class, 'userfaq'])->name('user.faq');
    Route::get('contact', [App\Http\Controllers\ContactController::class, 'userContact'])->name('user.contact');
    Route::get('invite-friend', [App\Http\Controllers\HomepageController::class, 'inviteFriend'])->name('user.invitefriend');
    Route::get('settings', [App\Http\Controllers\HomepageController::class, 'userSettings'])->name('user.settings');
    Route::get('tevini-card', [App\Http\Controllers\HomepageController::class, 'card'])->name('user.card');

    // ==================== Stripe Payment ====================
    Route::get('stripe', [StripePaymentController::class, 'stripe']);
    Route::post('stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');
    Route::get('/topup', [DonorController::class, 'stripeDonation'])->name('stripeDonation');

    // ==================== Charity Link ====================
    Route::post('/close-a-link', [CharityController::class, 'closecharityLink']);
    Route::post('/transfer-to-tdf', [UserController::class, 'transferToTDF']);
    Route::post('/check-currency-amount', [UserController::class, 'checkCurrencyAmount']);
    Route::get('/transfer-to-tdf', [UserController::class, 'gettransferToTDF'])->name('user.transfertdf');

    // ==================== Card Service ====================
    Route::get('card-service', [CardServiceController::class, 'index'])->name('userCardService');
    Route::post('cardprofile/store', [CardServiceController::class, 'cardprofilestore'])->name('cardprofile.store');

    // Apply For Card
    Route::get('apply-for-card', [CardServiceController::class, 'applyForCard'])->name('applyforcard');
    Route::post('apply-for-card', [CardServiceController::class, 'applyForCardstore'])->name('applyforcardstore');

    // Apply For Card Holder
    Route::get('apply-for-cardholder', [CardServiceController::class, 'applyForCardHolder'])->name('applyforcardholder');
    Route::post('apply-for-cardholder', [CardServiceController::class, 'applyForCardHolderStore'])->name('applyforcardholderstore');
    Route::get('update-cardholder', [CardServiceController::class, 'updateCardHolder'])->name('cardholderUpdate');
    Route::post('update-cardholder', [CardServiceController::class, 'updateCardHolderPost'])->name('cardholderUpdatePost');

    // Order Card
    Route::get('order-card', [CardServiceController::class, 'orderCard'])->name('orderCard');
    Route::post('order-card', [CardServiceController::class, 'orderCardStore'])->name('orderCardstore');

    // Card Activation
    Route::get('/card-activation/{id}', [CardServiceController::class, 'cardActivation'])->name('cardActivation')->middleware('prevent-back-history');
    Route::post('/card-activation', [CardServiceController::class, 'cardActivationstore'])->name('cardActivationstore');

    // Card Set Pin
    Route::get('/card-setpin/{id}', [CardServiceController::class, 'cardSetPin'])->name('cardSetPin')->middleware('prevent-back-history');
    Route::post('/card-setpin', [CardServiceController::class, 'cardSetPinstore'])->name('cardSetPinstore');

    // ==================== OTP Verification ====================
    Route::get('/mobile-verification', [OTPController::class, 'mobileVerify'])->name('mobileVerify')->middleware('prevent-back-history');
    Route::post('/mobile-verification', [OTPController::class, 'OtpConfirmation'])->name('send.sms')->middleware('prevent-back-history');
    Route::get('/status-verification', [OTPController::class, 'statusVerify'])->name('statusVerify')->middleware('prevent-back-history');
    Route::post('/status-verification', [OTPController::class, 'statusOtpConfirmation'])->name('status.sms')->middleware('prevent-back-history');
    Route::get('/activation-verification', [OTPController::class, 'activationVerify'])->name('activationVerify')->middleware('prevent-back-history');
    Route::post('/activation-verification', [OTPController::class, 'activationOtpConfirmation'])->name('activation.sms')->middleware('prevent-back-history');

    // Card Status Change
    Route::get('/card-status-change', [CardServiceController::class, 'cardStatusChange'])->name('cardStatusChange')->middleware('prevent-back-history');
    Route::post('/card-status-change', [CardServiceController::class, 'cardStatusChangeStore'])->name('cardStatusChangeStore');

    // ==================== Balance Transfer ====================
    Route::get('balance-transfer', [BalanceTransferController::class, 'balanceTransfer'])->name('balanceTransfer');
    Route::post('balance-transfer', [BalanceTransferController::class, 'balanceTransferStore'])->name('transfer.balance');
});