<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OneGiv\OneGivCardController;
use App\Http\Controllers\OneGiv\OneGivWebhookController;

// -----------------------------------------------
// User Routes 
// -----------------------------------------------
Route::group(['prefix' => 'user/', 'middleware' => ['auth', 'is_user']], function () {

    //  Order 
    Route::get('onegiv/order-card',         [OneGivCardController::class, 'orderCardForm'])->name('onegiv.ordercard.form');
    Route::post('onegiv/order-card',        [OneGivCardController::class, 'orderCardStore'])->name('onegiv.ordercard.store');

    //  List 
    Route::get('onegiv/my-cards',           [OneGivCardController::class, 'myCards'])->name('onegiv.mycards');

    // PIN Change 
    Route::get('onegiv/change-pin/{serial}',[OneGivCardController::class, 'changePinForm'])->name('onegiv.changepin.form');
    Route::post('onegiv/change-pin',        [OneGivCardController::class, 'changePinStore'])->name('onegiv.changepin.store');

    // Transactions 
    Route::get('onegiv/transactions',       [OneGivCardController::class, 'transactions'])->name('onegiv.transactions');

});

// -----------------------------------------------
// Webhook Routes — OneGiv
// -----------------------------------------------
Route::group(['prefix' => 'api/webhooks/onegiv', 'middleware' => ['onegiv.auth']], function () {

    Route::post('card-order-processed',     [OneGivWebhookController::class, 'cardOrderProcessed']);
    Route::post('approve-charity',          [OneGivWebhookController::class, 'approveCharity']);
    Route::post('transaction-request',      [OneGivWebhookController::class, 'transactionRequest']);
    Route::post('refund-request',           [OneGivWebhookController::class, 'refundRequest']);
    Route::post('notify',                   [OneGivWebhookController::class, 'notify']);

});

// routes/onegiv.php 
Route::get('onegiv/test-simulate/{orderNumber}', function($orderNumber) {
    $onegiv = new \App\Services\OneGivService();
    $result = $onegiv->simulateCardOrderProcessed($orderNumber);
    return response()->json($result);
})->middleware(['auth', 'is_user']);