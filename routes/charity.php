<?php


use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CharityController;




Route::middleware(['auth:sanctum,charity', 'verified']);
// charity part start
Route::group(['prefix' =>'charity/', 'middleware' => ['charity', 'charity.profile.complete']], function(){
    Route::get('/dashboard', [CharityController::class, 'charityDashboard'])->name('charityDashboard');
    Route::get('/profile', [CharityController::class, 'profileShow'])->name('charity.profile');
    Route::get('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard');
    Route::post('/charity-transaction', [CharityController::class, 'charityTransaction'])->name('tran_charity_dashboard_search');
    Route::post('/profile', [CharityController::class, 'updateProfile'])->name('charity_profileUpdate');

    
    Route::post('/urgent-request', [CharityController::class, 'urgentRequest'])->name('charity.urgent_request');
    
    Route::get('/create-a-link', [CharityController::class, 'charityLink'])->name('charity_link');
    Route::post('/create-a-link', [CharityController::class, 'charityLinkStore']);



    
    Route::get('/process-voucher', [CharityController::class, 'processVoucher'])->name('charity.processvoucher');
    Route::post('/pvoucher-store', [CharityController::class, 'pvoucherStore'])->name('charity.pvoucher.store');
    Route::get('/process-voucher/{id}', [CharityController::class, 'instReport'])->name('charity.instreport');
    Route::post('/pvr-mail', [CharityController::class, 'instReportmail'])->name('charity.instreportmail');

    
    Route::get('/pending-voucher', [CharityController::class, 'pendingVoucher'])->name('charity.pendingvoucher');

    
    Route::post('new-email-account', [CharityController::class, 'emailAccountStore'])->name('charity.emailAccountStore');
    Route::post('email-account-update', [CharityController::class, 'emailAccountUpdate'])->name('charity.emailAccountUpdate');
    Route::delete('user-email/delete/{id}', [CharityController::class, 'emaildestroy'])->name('charity.emailDestroy');



});