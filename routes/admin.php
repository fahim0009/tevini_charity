<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CharityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\User\UserController;

//admin part start
Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard')->middleware('is_admin');
    //profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('profile/{id}', [AdminController::class, 'adminProfileUpdate']);
    Route::post('changepassword', [AdminController::class, 'changeAdminPassword']);
    Route::put('image/{id}', [AdminController::class, 'adminImageUpload']);
    //profile end
    //admin registration
    Route::get('register','App\Http\Controllers\Admin\AdminController@adminindex');
    Route::post('register','App\Http\Controllers\Admin\AdminController@adminstore');
    Route::get('register/{id}/edit','App\Http\Controllers\Admin\AdminController@adminedit');
    Route::put('register/{id}','App\Http\Controllers\Admin\AdminController@adminupdate');
    Route::get('register/{id}', 'App\Http\Controllers\Admin\AdminController@admindestroy');
    //admin registration end
    //agent registration
    Route::get('agent-register','App\Http\Controllers\Admin\AdminController@agentindex');
    Route::post('agent-register','App\Http\Controllers\Admin\AdminController@agentstore');
    Route::get('agent-register/{id}/edit','App\Http\Controllers\Admin\AdminController@agentedit');
    Route::put('agent-register/{id}','App\Http\Controllers\Admin\AdminController@agentupdate');
    Route::get('agent-register/{id}', 'App\Http\Controllers\Admin\AdminController@agentdestroy');
    // certificate update
    // Route::post('image-upload', 'App\Http\Controllers\Admin\AdminController@agentCertificateUpdate')->name('image.upload.post');
    //agent registration end
    //user registration
    Route::get('user-register','App\Http\Controllers\Admin\AdminController@userindex');
    Route::post('user-register','App\Http\Controllers\Admin\AdminController@userstore');
    Route::get('user-register/{id}/edit','App\Http\Controllers\Admin\AdminController@useredit');
    Route::put('user-register/{id}','App\Http\Controllers\Admin\AdminController@userupdate');
    Route::get('user-register/{id}', 'App\Http\Controllers\Admin\AdminController@userdestroy');
    //user registration end
    //code master
    Route::resource('softcode','App\Http\Controllers\Admin\SoftcodeController');
    Route::resource('master','App\Http\Controllers\Admin\MasterController');
    //code master end
    //company details
    Route::resource('company-detail','App\Http\Controllers\Admin\CompanyDetailController');
    //company details end
    //slider
    Route::resource('sliders','App\Http\Controllers\Admin\SliderController');
    Route::get('activeslider','App\Http\Controllers\Admin\SliderController@activeslider');
    //slider end
    Route::resource('seo-settings','App\Http\Controllers\Admin\SeoSettingController');
    Route::resource('role','App\Http\Controllers\RoleController');
    Route::post('roleupdate','App\Http\Controllers\RoleController@roleUpdate');
    Route::resource('staff','App\Http\Controllers\StaffController');
    // transaction start
    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction');
    Route::post('/transaction', [TransactionController::class, 'index'])->name('transaction_search');
    Route::get('/report', [ReportController::class, 'index'])->name('report');

    // donor details
    Route::get('/donor', [DonorController::class, 'addDonor'])->name('donor');
    Route::post('/add-donor', [DonorController::class, 'donorStore'])->name('donor.store');
    Route::get('/edit-donor/{id}/edit', [DonorController::class, 'donorEdit'])->name('donor.edit');
    Route::post('/edit-donor/{id}', [DonorController::class, 'donorUpdate'])->name('donor.update');
    Route::post('/donor/delete', [DonorController::class, 'deleteDonor'])->name('deletedonor');
    Route::post('/add-account', [DonorController::class, 'addAccount']);
    Route::post('/update-overdrawn', [DonorController::class, 'updateOverdrawnAmount']);

    Route::get('/donor-profile/{id}', [UserController::class, 'profileinAdmin'])->name('donor.profile');
    Route::get('/donor-transaction/{id}', [TransactionController::class, 'donorTransaction'])->name('donor.tranview');
    Route::post('/donor-transaction/{id}', [TransactionController::class, 'donorTransaction'])->name('search.donortran');
    Route::get('/donation-record/{id}', [DonorController::class, 'userDonationrecodinAdmin'])->name('donor.donationrecord');
    Route::get('/standing-order/{id}', [DonorController::class, 'userStandingOrderinAdmin'])->name('donor.standingorder');
    Route::get('/donor-order-history/{id}', [OrderController::class, 'userOrderinAdmin'])->name('donor.orderhistory');
    Route::get('/donor-report/{id}', [DonorController::class, 'userReportinAdmin'])->name('donor.report');
    Route::post('/donor-report/{id}', [DonorController::class, 'userReportinAdmin'])->name('donor.reportsearch');;
    Route::post('/donor-report-mail', [DonorController::class, 'userReportMailinAdmin'])->name('donor.reportmail');
    Route::post('/reportall', [DonorController::class, 'multiUserreport'])->name('muliti.report');
    Route::get('/make-donation/{id}', [DonorController::class, 'userDonationAdmin'])->name('donor.donation');
    Route::post('/make-donation', [DonorController::class, 'userDonationAdminStore'])->name('donor.dnstore');


    Route::get('/donor-voucher-order/{id}', [OrderController::class, 'voucherinAdmin'])->name('donor.vorder');
    Route::post('/addvoucher', [OrderController::class, 'storeVoucher'])->name('voucher.store');

    Route::get('/charity-transaction/{id}', [TransactionController::class, 'charityTransaction'])->name('charity.tranview');
    Route::post('/charity-transaction/{id}', [TransactionController::class, 'charityTransaction'])->name('charity.tranview_search');

    // frontend
    Route::get('/about-help', [AboutController::class, 'aboutHelp'])->name('about.help');
    Route::post('/about-help', [AboutController::class, 'aboutHelpStore'])->name('about.helpstore');
    Route::get('/about-help/{id}', [AboutController::class, 'abouthelpedit'])->name('abouthelp.edit');
    Route::post('/about-help/{id}', [AboutController::class, 'abouthelpupdate'])->name('abouthelp.update');
    Route::get('/about-help/delete/{id}', [AboutController::class, 'abouthelpdelete'])->name('abouthelp.delete');
    Route::get('/about-content', [AboutController::class, 'aboutcontentshow'])->name('aboutcontent.show');
    Route::get('/about-content/{id}', [AboutController::class, 'aboutcontentedit'])->name('aboutcontent.edit');
    Route::post('/about-content/{id}', [AboutController::class, 'aboutcontentupdate'])->name('aboutcontent.update');
    Route::get('/donor-giving', [DonorController::class, 'adminDonorGiving'])->name('donorgiving');
    Route::get('/charity-list', [CharityController::class, 'index'])->name('charitylist');
    Route::post('/add-charity', [CharityController::class, 'store'])->name('charity.store');
    Route::get('/add-charity/{id}/edit', [CharityController::class, 'edit'])->name('charity.edit');
    Route::post('/add-charity/{id}', [CharityController::class, 'update'])->name('charity.update');
    Route::get('/add-charity/delete/{id}', [CharityController::class, 'deleteCharity'])->name('deletecharity');
    Route::get('/remittance', [TransactionController::class, 'remittance'])->name('remittance');
    Route::post('/remittance', [TransactionController::class, 'remittance'])->name('remittance.search');
    Route::get('/admin-contact', [ContactController::class, 'admincontact'])->name('admin.contact');
    Route::get('/admin-contact-mail', [ContactController::class, 'contactMail'])->name('admin.contactmail');
    Route::post('/admin-contact-mail/{id}', [ContactController::class, 'mailUpdate'])->name('contactmail.update');
    Route::get('/admin-contact-mail/edit/{id}', [ContactController::class, 'ContactmailEdit'])->name('contactmail.edit');
    Route::get('/news', [HomepageController::class, 'news'])->name('news');
    Route::get('/faq', [HomepageController::class, 'faq'])->name('faq');
    Route::get('/settings', [HomepageController::class, 'adminSettings'])->name('admin.settings');

    
    // active deactive charity
    Route::get('active-charity', [CharityController::class, 'activeCharity']);

    // topup
    Route::get('/topup/{id}/{amount}', [DonorController::class, 'topup'])->name('topup');
    Route::post('/topupstore', [DonorController::class, 'topupStore'])->name('topup.store');
    Route::get('/charity-topup/{id}', [CharityController::class, 'topup'])->name('charity.topup');
    Route::get('/charity-pay/{id}', [CharityController::class, 'pay'])->name('charity.pay');
    Route::post('/charity-topupstore', [CharityController::class, 'topupStore'])->name('charity.topup.store');
    Route::post('/charity-paystore', [CharityController::class, 'payStore'])->name('charity.pay.store');

    // voucher book
    Route::get('/voucher-book', [OrderController::class, 'voucherBookStock'])->name('voucherbooks');
    Route::post('/add-stock', [OrderController::class, 'addStock']);
    Route::get('/process-voucher', [OrderController::class, 'processVoucher'])->name('processvoucher');
    Route::get('/process-voucher/{id}', [OrderController::class, 'instReport'])->name('instreport');
    Route::post('/pvr-mail', [OrderController::class, 'instReportmail'])->name('instreportmail');
    Route::post('/pvoucher-store', [OrderController::class, 'pvoucherStore'])->name('pvoucher.store');
    Route::post('/pvoucher-draft', [OrderController::class, 'pvoucherDraft'])->name('pvoucher.draft');

    Route::get('/voucher-order', [OrderController::class, 'orderVoucher'])->name('ordervoucher');
    Route::get('/complete-voucher', [OrderController::class, 'completeVoucher'])->name('completevoucher');
    Route::get('/pending-voucher', [OrderController::class, 'pendingVoucher'])->name('pendingvoucher');
    Route::post('/pvcomplete', [OrderController::class, 'pvComplete']);
    Route::get('/waiting-voucher', [OrderController::class, 'waitingVoucher'])->name('waitingvoucher');
    Route::post('/waiting-vouchercomplete', [OrderController::class, 'watingvoucherComplete']);
    Route::post('/waiting-vouchercancel', [OrderController::class, 'watingvoucherCancel']);
    Route::post('/waiting-vouchermail', [OrderController::class, 'watingvoucherMail']);
    Route::post('/waiting-voucherimgadd', [OrderController::class, 'watingvoucherImageadd']);

    Route::post('/pvcancel', [OrderController::class, 'pvCancel']);
    Route::get('/single-order/{id}', [OrderController::class, 'singleOrder'])->name('singleorder');
    Route::get('/barcode/{id}', [OrderController::class, 'barcode'])->name('barcode');
    Route::post('/find-name', [OrderController::class, 'findName']);
    Route::post('/barcode', [OrderController::class, 'getbarCode']);

    //order
    Route::post('/order-status', [OrderController::class, 'orderStatus']);
    Route::get('/order/new', [OrderController::class, 'newOrder'])->name('neworder');
    Route::get('/order/complete', [OrderController::class, 'completeOrder'])->name('completeorder');
    Route::get('/order/cencle', [OrderController::class, 'cancelOrder'])->name('cancelorder');
    Route::post('/add-start-barcode', [OrderController::class, 'addStartBarcode']);
    Route::post('/add-pages', [OrderController::class, 'addNumberofpage']);

    // commission
    Route::get('/commission', [OrderController::class, 'commission'])->name('commission');

    // donation
    Route::get('/donation/list', [DonorController::class, 'donationlist'])->name('donationlist');
    Route::get('/donation/standing', [DonorController::class, 'donationStanding'])->name('donationstanding');
    Route::get('/donation/record', [DonorController::class, 'donationRecord'])->name('donationrecord');
    Route::post('/donation-status', [DonorController::class, 'donationStatus']);

    // Campaign
    Route::get('/campaign', [CampaignController::class, 'index'])->name('campaign');
    Route::post('/add-campaign', [CampaignController::class, 'store'])->name('campaign.store');
    Route::get('/edit-campaign/{id}/edit', [CampaignController::class, 'edit'])->name('campaign.edit');
    Route::post('/edit-campaign/{id}', [CampaignController::class, 'update'])->name('campaign.update');
    Route::post('/campaign/delete', [CampaignController::class, 'delete'])->name('deletecampaign');
    Route::post('/update-url', [CampaignController::class, 'updateUrl'])->name('updateurl');

    // gateway
    Route::resource('gateway','App\Http\Controllers\GatewayController');

    // Notification
    Route::post('/donornoti', [DashboardController::class, 'donorNoti'])->name('donornoti');
    Route::post('/ordernoti', [DashboardController::class, 'orderNoti'])->name('ordernoti');
    Route::post('/donationnoti', [DashboardController::class, 'donationNoti'])->name('donationnoti');
    Route::post('/topupnoti', [DashboardController::class, 'topupNoti'])->name('topupnoti');

    // stripe topup
    Route::get('/stripe-topup', [StripePaymentController::class, 'stripetopup'])->name('stripetopup');
    Route::post('/stripe-topup-status', [StripePaymentController::class, 'stripetopupstatus']);

});
