<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DonationController;
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
use App\Http\Controllers\QpayBalanceController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductFeeController;
use App\Http\Controllers\Admin\CardProfileController;
use App\Http\Controllers\Admin\CompanyDetailController;
use App\Http\Controllers\Admin\SpendProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TDFTransactionController;
use App\Http\Controllers\Admin\DonorBalanceController;
use App\Http\Controllers\Admin\ProcessVoucherController;
use App\Http\Controllers\Admin\CredentialController;
use App\Http\Controllers\Admin\VouchersController;
use App\Http\Controllers\Agent\AgentController;
use App\Http\Controllers\BalanceTransferController;
use App\Http\Controllers\ExpiredVoucherController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\TopupController;
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
    Route::resource('company-detail', CompanyDetailController::class);
    
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
    Route::get('/admin/donors/data', [DonorController::class, 'donorData'])->name('donors.data');

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
    Route::get('/donor-topup-report/{id}', [DonorController::class, 'userTopReportinAdmin'])->name('donor.topupreport');
    Route::get('/donor-topup-report-show/{id}', [DonorController::class, 'userTopReportShowinAdmin'])->name('topup.reportShow');
    Route::post('/donor-report/{id}', [DonorController::class, 'userReportinAdmin'])->name('donor.reportsearch');;
    Route::post('/donor-report-mail', [DonorController::class, 'userReportMailinAdmin'])->name('donor.reportmail');
    Route::post('/donor-topup-report-mail', [DonorController::class, 'userTopupReportMailinAdmin'])->name('donor.topupreportmail');
    Route::post('/reportall', [DonorController::class, 'multiUserreport'])->name('muliti.report');
    Route::get('/make-donation/{id}', [DonorController::class, 'userDonationAdmin'])->name('donor.donation');
    Route::post('/make-donation', [DonorController::class, 'userDonationAdminStore'])->name('donor.dnstore');
    Route::post('/make-stnddonation', [DonorController::class, 'userstandingDonationAdminStore']);
    Route::get('/tdf-transfer/{id}', [DonorController::class, 'tdfTransferAdmin'])->name('donor.tdftransfer');
    Route::post('/tdf-transfer', [DonorController::class, 'tdfTransferStore'])->name('donor.tdftransferstore');

    Route::put('/donor/update-gift-aid/{id}', [DonorController::class, 'updateGiftAid'])->name('donor.update_gift_aid');


    Route::get('/donor-voucher-order/{id}', [OrderController::class, 'voucherinAdmin'])->name('donor.vorder');
    Route::get('/donor-voucher-order-edit/{id}', [OrderController::class, 'voucherEditinAdmin'])->name('donor.vorderEdit');
    Route::get('/donor-voucher-report/{id}', [OrderController::class, 'voucherReportinAdmin'])->name('donor.vorderReport');
    Route::post('/addvoucher', [OrderController::class, 'storeVoucher'])->name('voucher.store');
    Route::post('/voucher-update', [OrderController::class, 'updateVoucher'])->name('voucher.update');

    Route::get('/charity-transaction/{id}', [TransactionController::class, 'charityTransaction'])->name('charity.tranview');
    Route::post('/charity-transaction/{id}', [TransactionController::class, 'charityTransaction'])->name('charity.tranview_search');

    // test message 
    Route::post('/text-message/{id}', [OTPController::class, 'sendText'])->name('admin.donor.sendtext');




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
    Route::get('/admin/charity-data', [CharityController::class, 'getData'])->name('charity.data');

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

    // donor email
    Route::get('/donor-custom-mail', [DonorController::class, 'addDonorMail'])->name('admin.donor.email');
    Route::post('/donor-custom-mail', [DonorController::class, 'donorMailSend'])->name('allDonorMailSend');
    Route::get('/send-email/{id}', [DonorController::class, 'sendemail'])->name('sendemail');
    Route::post('/send-email', [DonorController::class, 'mailsend'])->name('mailsend');


    // topup
    Route::get('/topup/{id}/{amount}', [DonorController::class, 'topup'])->name('topup');
    Route::post('/topupstore', [DonorController::class, 'topupStore'])->name('topup.store');
    Route::get('/charity-topup/{id}/{amount?}', [CharityController::class, 'topup'])->name('charity.topup');
    Route::get('/charity-pay/{id}/{amount?}', [CharityController::class, 'pay'])->name('charity.pay');
    Route::post('/charity-topupstore', [CharityController::class, 'topupStore'])->name('charity.topup.store');
    Route::post('/charity-paystore', [CharityController::class, 'payStore'])->name('charity.pay.store');

    
    // charity email
    Route::get('/charity-email/{id}', [CharityController::class, 'charityemail'])->name('charityemail');
    Route::post('/charity-email', [CharityController::class, 'charitymailsend'])->name('charitymailsend');

    // voucher book
    Route::get('/voucher-book', [OrderController::class, 'voucherBookStock'])->name('voucherbooks');
    Route::post('/add-stock', [OrderController::class, 'addStock']);
    Route::get('/process-voucher', [OrderController::class, 'processVoucher'])->name('processvoucher');
    Route::get('/process-voucher/{id}', [OrderController::class, 'instReport'])->name('instreport');
    Route::post('/pvr-mail', [OrderController::class, 'instReportmail'])->name('instreportmail');
    Route::post('/pvoucher-store', [OrderController::class, 'pvoucherStore'])->name('pvoucher.store');
    Route::post('/pvoucher-draft', [OrderController::class, 'pvoucherDraft'])->name('pvoucher.draft');

    // upload process voucher pdf
    Route::post('/upload-barcode-pdf', [ProcessVoucherController::class, 'uploadAndExtract']);
    Route::post('/pdf-to-text', [ProcessVoucherController::class, 'uploadAndExtractMultiplepdf'])->name('pdfToText');
    Route::post('/add-to-process', [ProcessVoucherController::class, 'addToProcessBarcode'])->name('addToProcessBarcode');
    Route::post('/delete-process-voucher-list', [ProcessVoucherController::class, 'deleteProcessBarcode'])->name('delete-process-voucher-list');
    Route::post('/delete-process-voucher-image-list', [ProcessVoucherController::class, 'deleteProcessBarcodeImage'])->name('delete-process-voucher-image-list');
    Route::post('/delete-processed-single-barcode', [ProcessVoucherController::class, 'deleteProcessSingleBarcode']);


    Route::get('/voucher-order', [OrderController::class, 'orderVoucher'])->name('ordervoucher');
    Route::get('/complete-voucher/{id?}', [OrderController::class, 'completeVoucher'])->name('completevoucher');
    Route::get('/pending-voucher/{id?}', [OrderController::class, 'pendingVoucher'])->name('pendingvoucher');
    Route::post('/pvcomplete', [OrderController::class, 'pvComplete']);
    Route::get('/waiting-voucher', [OrderController::class, 'waitingVoucher'])->name('waitingvoucher');
    Route::post('/waiting-vouchercomplete', [OrderController::class, 'watingvoucherComplete']);
    Route::post('/waiting-vouchercancel', [OrderController::class, 'watingvoucherCancel']);
    Route::post('/waiting-vouchermail', [OrderController::class, 'watingvoucherMail']);
    Route::post('/waiting-voucherimgadd', [OrderController::class, 'watingvoucherImageadd']);


    // expired voucher 
    Route::get('/expired-voucher', [ExpiredVoucherController::class, 'getExpiredVoucher'])->name('expiredVoucher');



    Route::post('/pvcancel', [OrderController::class, 'pvCancel']);
    Route::get('/single-order/{id}', [OrderController::class, 'singleOrder'])->name('singleorder');
    Route::get('/barcode/{id}', [OrderController::class, 'barcode'])->name('barcode');
    Route::post('/find-name', [OrderController::class, 'findName']);
    Route::post('/barcode', [OrderController::class, 'getbarCode']);

    Route::get('/download-postage/{id}', [OrderController::class, 'downloadpostage'])->name('downloadpostage');

    //order
    Route::post('/order-status', [OrderController::class, 'orderStatus'])->name('admin.order.status');
    Route::get('/order/new', [OrderController::class, 'newOrder'])->name('neworder');
    Route::get('/order/complete', [OrderController::class, 'completeOrder'])->name('completeorder');
    Route::get('/order/cancel', [OrderController::class, 'cancelOrder'])->name('cancelorder');
    Route::post('/add-start-barcode', [OrderController::class, 'addStartBarcode']);
    Route::post('/add-pages', [OrderController::class, 'addNumberofpage']);
    Route::post('/cancel-pages', [OrderController::class, 'cancelVoucherBook']);

    // commission
    Route::get('/commission', [OrderController::class, 'commission'])->name('commission');

    // donation
    Route::get('/donation/list', [DonorController::class, 'donationlist'])->name('donationlist');
    Route::get('/donation/pending-list', [DonorController::class, 'pendingdonationlist'])->name('pendingdonationlist');
    Route::get('/donation/record', [DonorController::class, 'donationRecord'])->name('donationrecord');
    Route::post('/donation-status', [DonorController::class, 'donationStatus']);
    Route::post('/donation-complete', [DonorController::class, 'donationComplete']);

    // standing order 
    Route::get('/donation/standing', [DonationController::class, 'donationStanding'])->name('donationstanding');
    Route::get('/donation/standing/{id}', [DonationController::class, 'singledonationStanding'])->name('singlestanding');
    // active deactive standing order
    Route::post('active-standingdonation', [DonationController::class, 'activeStandingdnsn']);
    Route::get('cron-job', [DonationController::class, 'stdTest']);



    // Campaign
    Route::get('/campaign', [CampaignController::class, 'index'])->name('campaign');
    Route::get('/admin/campaign/data', [CampaignController::class, 'campaignData'])->name('campaign.data');
    Route::post('/campaign', [CampaignController::class, 'index'])->name('campaign.search');
    Route::post('/add-campaign', [CampaignController::class, 'store'])->name('campaign.store');
    Route::get('/edit-campaign/{id}/edit', [CampaignController::class, 'edit'])->name('campaign.edit');
    Route::post('/edit-campaign/{id}', [CampaignController::class, 'update'])->name('campaign.update');
    Route::post('/campaign/delete', [CampaignController::class, 'delete'])->name('deletecampaign');
    Route::post('/update-url', [CampaignController::class, 'updateUrl'])->name('updateurl');
    Route::post('/campaign-report', [CampaignController::class, 'campaignReport'])->name('campaignReport');

    Route::get('/cmpgn/donor-list/{id?}', [CampaignController::class, 'getAllCampaignDonor'])->name('campaign.donor_list');

    // gateway
    Route::resource('gateway','App\Http\Controllers\GatewayController');

    // Notification
    Route::post('/donornoti', [DashboardController::class, 'donorNoti'])->name('donornoti');
    Route::post('/ordernoti', [DashboardController::class, 'orderNoti'])->name('ordernoti');
    Route::post('/donationnoti', [DashboardController::class, 'donationNoti'])->name('donationnoti');
    Route::post('/topupnoti', [DashboardController::class, 'topupNoti'])->name('topupnoti');

    // stripe topup
    Route::get('/stripe-topup', [TopupController::class, 'stripetopup'])->name('stripetopup');
    Route::post('/stripe-topup-status', [StripePaymentController::class, 'stripetopupstatus']);

    // Product Fee
    Route::get('/productfee', [ProductFeeController::class, 'index'])->name('productfee');
    Route::post('/add-productfee', [ProductFeeController::class, 'store'])->name('productfee.store');
    
    // card profile
    Route::get('/cardprofile', [CardProfileController::class, 'cardprofile'])->name('cardprofile');
    Route::get('/cardprofile/{id}', [CardProfileController::class, 'cardprofileview'])->name('cardprofileview');
    Route::get('/cardprofile/edit/{id}', [CardProfileController::class, 'cardprofileEdit'])->name('cardprofileedit');
    Route::get('/cardprofile/limite/{id}', [CardProfileController::class, 'cardprofileLimite'])->name('cardprofilelimite');
    Route::post('/cardprofile/update', [CardProfileController::class, 'productfeeUpdate'])->name('cardprofile.update');
    Route::post('/cardprofile/limite/update', [CardProfileController::class, 'productfeeLimiteUpdate'])->name('cardprofile.limiteupdate');


    // spend profile
    Route::get('/spend-profile', [SpendProfileController::class, 'index'])->name('spendprofile');
    Route::post('/spend-profile', [SpendProfileController::class, 'store'])->name('spendprofilestore');

    // Step 2 Products
    Route::get('/product/index', [ProductController::class, 'index'])->name('product.index');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/product/view/{id}', [ProductController::class, 'view'])->name('product.view');

    
    Route::get('/authorisation', [ProductController::class, 'getAuthorisation'])->name('authorisation');
    Route::get('/authorisation/{id}', [ProductController::class, 'getAuthorisationDetails'])->name('authorisation.details');
    Route::get('/settlement', [ProductController::class, 'getSettlement'])->name('settlement');
    
    Route::get('/expired', [ProductController::class, 'getExpired'])->name('expired');
    Route::get('/expired/{id}', [ProductController::class, 'getExpiredDetails'])->name('expiredDetails');

    
    Route::get('/card-transaction', [ProductController::class, 'getCardTransaction'])->name('cardTransaction');
    Route::get('/user-card-transaction', [ProductController::class, 'getUserCardTransaction'])->name('cardservice.tran');

    // qpay balance 
    Route::get('/qpay-balance', [QpayBalanceController::class, 'index'])->name('qpaybalance');
    Route::get('/qpay-balance/{id}', [QpayBalanceController::class, 'edit'])->name('qpaybalance.edit');
    Route::post('/qpay-balance/{id}', [QpayBalanceController::class, 'update'])->name('qpaybalance.update');
    Route::get('/qpay-add-balance', [QpayBalanceController::class, 'add'])->name('qpaybalance.add');
    Route::post('/qpay-add-balance', [QpayBalanceController::class, 'store'])->name('qpaybalance.store');

    // tdf transaction
    Route::get('/tdf-transaction', [TDFTransactionController::class, 'getTDFTransaction'])->name('tdfTransaction');
    Route::get('/tdf-transaction-complete', [TDFTransactionController::class, 'getTDFTransactionComplete'])->name('tdfTransactionComplete');
    Route::get('/tdf-transaction-cancel', [TDFTransactionController::class, 'getTDFTransactionCancel'])->name('tdfTransactionCancel');
    Route::post('/tdf-transaction', [TDFTransactionController::class, 'tdfBlanceStore'])->name('tdfTransaction.update');
    Route::post('/tdf-transaction-status', [TDFTransactionController::class, 'changeStatus']);

    // balance transfer
    Route::get('/balance-transfer', [BalanceTransferController::class, 'getBalanceTransferByAdmin'])->name('admin.balanceTransfer');
    Route::get('/donor-balance-transfer/{id}', [BalanceTransferController::class, 'getDonorBalanceTransferByAdmin'])->name('donor.balanceTransfer');
    Route::post('/balance-transfer-status', [BalanceTransferController::class, 'changeStatus']);

    

    // donor balance check
    Route::get('/get-donor-balance', [DonorBalanceController::class, 'index'])->name('donorBalance');
    Route::post('/get-donor-balance', [DonorBalanceController::class, 'balanceUpdate']);

    // voucher search
    Route::get('/get-voucher', [VouchersController::class, 'getVoucher'])->name('getVoucher');
    Route::post('/get-voucher', [VouchersController::class, 'getVoucher'])->name('voucherSearch');



    // get users account delete request
    Route::get('/user-delete-request', [AdminController::class, 'getUserDeleteRequest'])->name('allUserDeleteReq');


    // barcode delete
    Route::get('/barcode-delete', [VouchersController::class, 'getBarcode'])->name('admin.getBarcode');
    Route::post('/barcode-delete', [VouchersController::class, 'getBarcode'])->name('admin.getBarcodeSearch');
    Route::post('/all-barcode-delete', [VouchersController::class, 'deleteBarcode'])->name('admin.deleteBarcode');

    
    Route::get('/transaction-delete', [TransactionController::class, 'checkTran'])->name('admin.transactionDelete');
    Route::post('/transaction-delete', [TransactionController::class, 'checkTran'])->name('admin.transactionSearch');
    Route::post('/transaction-status-change', [TransactionController::class, 'changeTranStatus'])->name('admin.transactionChangeStatus');



    
    Route::post('/new-information', [CredentialController::class, 'newUserCredentialStore'])->name('newUserCredentialStore');
    Route::put('/user-email/update/{id}', [CredentialController::class, 'update'])->name('useremail.update');
    Route::delete('/user-email/delete/{id}', [CredentialController::class, 'destroy'])->name('useremail.destroy');
    Route::post('/useremail-store', [CredentialController::class, 'charityEmailStore'])->name('useremail.store');
    Route::post('/useremail-update', [CredentialController::class, 'charityEmailupdate'])->name('charityemail.update');




    // order history check for 360 amount order
    Route::get('/check-some-order', [VouchersController::class, 'checkOrder'])->name('checkOrder');


});
