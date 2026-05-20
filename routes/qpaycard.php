<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\ProductFeeController;
use App\Http\Controllers\Admin\CardProfileController;
use App\Http\Controllers\Admin\SpendProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\QpayBalanceController;

/*
|--------------------------------------------------------------------------
| QPay Card Routes (Included in Admin Routes)
|--------------------------------------------------------------------------
*/

// Product Fee
Route::get('/productfee', [ProductFeeController::class, 'index'])->name('productfee');
Route::post('/add-productfee', [ProductFeeController::class, 'store'])->name('productfee.store');

// Card Profile
Route::get('/cardprofile', [CardProfileController::class, 'cardprofile'])->name('cardprofile');
Route::get('/cardprofile/{id}', [CardProfileController::class, 'cardprofileview'])->name('cardprofileview');
Route::get('/cardprofile/edit/{id}', [CardProfileController::class, 'cardprofileEdit'])->name('cardprofileedit');
Route::get('/cardprofile/limite/{id}', [CardProfileController::class, 'cardprofileLimite'])->name('cardprofilelimite');
Route::post('/cardprofile/update', [CardProfileController::class, 'productfeeUpdate'])->name('cardprofile.update');
Route::post('/cardprofile/limite/update', [CardProfileController::class, 'productfeeLimiteUpdate'])->name('cardprofile.limiteupdate');

// Spend Profile
Route::get('/spend-profile', [SpendProfileController::class, 'index'])->name('spendprofile');
Route::post('/spend-profile', [SpendProfileController::class, 'store'])->name('spendprofilestore');

// Products
Route::get('/product/index', [ProductController::class, 'index'])->name('product.index');
Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
Route::get('/product/view/{id}', [ProductController::class, 'view'])->name('product.view');

// Authorisation
Route::get('/authorisation', [ProductController::class, 'getAuthorisation'])->name('authorisation');
Route::get('/authorisation/{id}', [ProductController::class, 'getAuthorisationDetails'])->name('authorisation.details');

// Settlement
Route::get('/settlement', [ProductController::class, 'getSettlement'])->name('settlement');

// Expired
Route::get('/expired', [ProductController::class, 'getExpired'])->name('expired');
Route::get('/expired/{id}', [ProductController::class, 'getExpiredDetails'])->name('expiredDetails');

// Card Transaction
Route::get('/card-transaction', [ProductController::class, 'getCardTransaction'])->name('cardTransaction');
Route::get('/user-card-transaction', [ProductController::class, 'getUserCardTransaction'])->name('cardservice.tran');

// QPay Balance
Route::get('/qpay-balance', [QpayBalanceController::class, 'index'])->name('qpaybalance');
Route::get('/qpay-balance/{id}', [QpayBalanceController::class, 'edit'])->name('qpaybalance.edit');
Route::post('/qpay-balance/{id}', [QpayBalanceController::class, 'update'])->name('qpaybalance.update');
Route::get('/qpay-add-balance', [QpayBalanceController::class, 'add'])->name('qpaybalance.add');
Route::post('/qpay-add-balance', [QpayBalanceController::class, 'store'])->name('qpaybalance.store');