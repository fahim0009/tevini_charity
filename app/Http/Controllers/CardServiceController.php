<?php

namespace App\Http\Controllers;

use App\Models\Authorisation;
use App\Models\CardHolder;
use App\Models\CardOrder;
use App\Models\CardProduct;
use App\Models\CardStatus;
use App\Models\Expired;
use App\Models\MobileVerify;
use App\Models\PurchaseHistory;
use App\Models\User;
use App\Models\Settlement;
use App\Models\Transaction;
use App\Models\Usertransaction;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Exception;
use Twilio\Rest\Client;
use Carbon\Carbon;

class CardServiceController extends Controller
{
    public function index()
    {
        $id = Auth::user()->CreditProfileId;

        if (isset($id)) {
            try {
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/creditProfile/'.$id.'', [
                        'Accept' => 'application/json',
                    ]);
                
                $data = $response->json();
                $CardHolderId = CardHolder::where('user_id', Auth::user()->id)->first();
                return view('frontend.user.card.index', compact('data','CardHolderId'));
            } catch (\Exception $e) {
                // Handle exception
                return view('error', ['message' => $e->getMessage()]);
            }
        } else {
            $data = "";
            $CardHolderId = "";
            return view('frontend.user.card.index', compact('data','CardHolderId'));
        }
        

    }

    public function cardprofilestore(Request $request)
    {
        $ProfileName = Auth::user()->name;
        $CreditLimit =  100000;
        $IsPrePaid = true;

        $avlblBalnce = Auth::user()->balance - $CreditLimit;

        // Send a POST request to the API with the updated finance fee value
        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/creditProfile', [
                'ProfileName' => $ProfileName,
                'CreditLimit' => $CreditLimit,
                'IsPrePaid' => $IsPrePaid,
            ]);

            $data = $response->json();
            $userupdate = User::find(Auth::user()->id);
            $userupdate->CreditProfileId = $data['CreditProfile']['CreditProfileId'];
            $userupdate->save();
    
        // Check the response status code to see if the update was successful
        if ($response->ok()) {

            // update available balance 
            // card balance update
            if (isset($userupdate->CreditProfileId)) {
                $CreditProfileId = $userupdate->CreditProfileId;
                $CreditProfileName = $userupdate->name;
                $AvailableBalance = $avlblBalnce;
                $comment = "Balance store";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end




            // apply for product start
            $spendProfile = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/spendProfile/1', [
                    'Accept' => 'application/json',
                ]);
                
            $sProfile = $spendProfile->json();

            $ProductCode = time().Auth::user()->id;
            $Currency = "GBP";
            $CardProgram = "TEVINI PROGRAM";
            $CardDesign = "TEVINI";
            $CardProduct = "TEVINI CARD PRODUCT";
            $FundsOwnership = "1";
            $ProductType = "Tevini Donor Card";
            $IsActive = true;
            $IsVirtual = false;
            $CorporateLoadMaxBalance = "";
            $CorporateLoadMinLoadAmount = "";
            $MaxDailyBalance = "";
            $MaxDailyLoad = "";
            $MaxWeeklyLoad = "";
            $MaxMonthlyLoad = "";
            $MaxYearlyLoad = "";
            $Reloadable = true;
            $ForEmployee = true;
            $OnlineTransactions = true;
            $ATMTransactions = false;
            $DebitFeePercentage = "";
            $CreditFeePercentage = "";
            $AutoTopupEnabled = false;
            $AutoTopupDefaultAmount = "";
            $AutoTopUpTopUpThreshold = "";
            $TokeniseWithoutPaymentCardEnabled = true;
            $MaxTopupValue = "";
            $MinTopupValue = "";
            $RewardsPointsEnabled = false;
            $RewardsPointPerUnit = 0;
            $AllowAnonymousPinReveal = false;
            $KycCheck = false;
            $PepsSanctionsCheck = false;
            $SpendProfileId = $sProfile['SpendProfile']['SpendProfileId'];
            $SpendProfileName = $sProfile['SpendProfile']['ProfileName'];
            $CreditProfileId = $data['CreditProfile']['CreditProfileId'];
            $CreditProfileName = $userupdate->name;
            $FundingSource = 2;
            $TransactionAlerts = true;


            // Send a POST request to the API with the updated finance fee value
            $productResponse = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product', [
                'ProductCode' => $ProductCode,
                'Currency' => $Currency,
                'CardProgram' => $CardProgram,
                'CardDesign' => $CardDesign,
                'CardProduct' => $CardProduct,
                'FundsOwnership' => $FundsOwnership,
                'ProductType' => $ProductType,
                'IsActive' => $IsActive,
                'IsVirtual' => $IsVirtual,
                'CorporateLoadMaxBalance' => $CorporateLoadMaxBalance,
                'CorporateLoadMinLoadAmount' => $CorporateLoadMinLoadAmount,
                'MaxDailyBalance' => $MaxDailyBalance,
                'MaxDailyLoad' => $MaxDailyLoad,
                'MaxWeeklyLoad' => $MaxWeeklyLoad,
                'MaxMonthlyLoad' => $MaxMonthlyLoad,
                'MaxYearlyLoad' => $MaxYearlyLoad,
                'Reloadable' => $Reloadable,
                'ForEmployee' => $ForEmployee,
                'OnlineTransactions' => $OnlineTransactions,
                'ATMTransactions' => $ATMTransactions,
                'DebitFeePercentage' => $DebitFeePercentage,
                'CreditFeePercentage' => $CreditFeePercentage,
                'AutoTopupEnabled' => $AutoTopupEnabled,
                'AutoTopupDefaultAmount' => $AutoTopupDefaultAmount,
                'AutoTopUpTopUpThreshold' => $AutoTopUpTopUpThreshold,
                'TokeniseWithoutPaymentCardEnabled' => $TokeniseWithoutPaymentCardEnabled,
                'MaxTopupValue' => $MaxTopupValue,
                'MinTopupValue' => $MinTopupValue,
                'RewardsPointsEnabled' => $RewardsPointsEnabled,
                'RewardsPointPerUnit' => $RewardsPointPerUnit,
                'AllowAnonymousPinReveal' => $AllowAnonymousPinReveal,
                'KycCheck' => $KycCheck,
                'PepsSanctionsCheck' => $PepsSanctionsCheck,
                'SpendProfileId' => $SpendProfileId,
                'SpendProfileName' => $SpendProfileName,
                'CreditProfileId' => $CreditProfileId,
                'CreditProfileName' => $request->CreditProfileName,
                'FundingSource' => $FundingSource,
                'TransactionAlerts' => $TransactionAlerts,
            ]);


            // Check the response status code to see if the update was successful
            if ($productResponse->ok()) {

                $cardproduct = new CardProduct();
                $cardproduct->user_id = Auth::user()->id;
                $cardproduct->ProductCode = $ProductCode;
                $cardproduct->save();
                return redirect()->route('userCardService')->with('successmsg', 'Card Product Create Successfully!');
            } else {
                return redirect()->back()->with('error', 'Unable to create product.');
            }
            // apply for product end
            return redirect()->back()->with('success', 'Credit Profile Request Create Successfully!');
        } else {
            return redirect()->back()->with('error', 'Unable to update credit profile.');
        }
    }

    // public function applyForCard()
    // {
    //     $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
    //             ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/spendProfile/2', [
    //                 'Accept' => 'application/json',
    //             ]);
                
    //     $data = $response->json();

    //     // dd($data);

    //     return view('frontend.user.card.applyforcard', compact('data'));
    // }

    // public function applyForCardstore(Request $request)
    // {
    //     $ProductCode = time().Auth::user()->id;
    //     $Currency = "GBP";
    //     $CardProgram = $request->input('CardProgram');
    //     $CardDesign = "TEVINI";
    //     $CardProduct = $request->input('CardProduct');
    //     $FundsOwnership = "1";
    //     $ProductType = $request->input('ProductType');
    //     $IsActive = true;
    //     $IsVirtual = false;
    //     $CorporateLoadMaxBalance = "";
    //     $CorporateLoadMinLoadAmount = "";
    //     $MaxDailyBalance = "";
    //     $MaxDailyLoad = "";
    //     $MaxWeeklyLoad = "";
    //     $MaxMonthlyLoad = "";
    //     $MaxYearlyLoad = "";
    //     $Reloadable = true;
    //     $ForEmployee = true;
    //     $OnlineTransactions = true;
    //     $ATMTransactions = false;
    //     $DebitFeePercentage = "";
    //     $CreditFeePercentage = "";
    //     $AutoTopupEnabled = false;
    //     $AutoTopupDefaultAmount = "";
    //     $AutoTopUpTopUpThreshold = "";
    //     $TokeniseWithoutPaymentCardEnabled = true;
    //     $MaxTopupValue = "";
    //     $MinTopupValue = "";
    //     $RewardsPointsEnabled = false;
    //     $RewardsPointPerUnit = 0;
    //     $AllowAnonymousPinReveal = false;
    //     $KycCheck = false;
    //     $PepsSanctionsCheck = false;
    //     $SpendProfileId = $request->input('SpendProfileId');
    //     $SpendProfileName = $request->input('SpendProfileName');
    //     $CreditProfileId = $request->input('CreditProfileId');
    //     $CreditProfileName = $request->input('CreditProfileName');
    //     $FundingSource = 2;
    //     $TransactionAlerts = true;

    //     // Send a POST request to the API with the updated finance fee value
    //     $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
    //         ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product', [
    //             'ProductCode' => $ProductCode,
    //             'Currency' => $Currency,
    //             'CardProgram' => $CardProgram,
    //             'CardDesign' => $CardDesign,
    //             'CardProduct' => $CardProduct,
    //             'FundsOwnership' => $FundsOwnership,
    //             'ProductType' => $ProductType,
    //             'IsActive' => $IsActive,
    //             'IsVirtual' => $IsVirtual,
    //             'CorporateLoadMaxBalance' => $CorporateLoadMaxBalance,
    //             'CorporateLoadMinLoadAmount' => $CorporateLoadMinLoadAmount,
    //             'MaxDailyBalance' => $MaxDailyBalance,
    //             'MaxDailyLoad' => $MaxDailyLoad,
    //             'MaxWeeklyLoad' => $MaxWeeklyLoad,
    //             'MaxMonthlyLoad' => $MaxMonthlyLoad,
    //             'MaxYearlyLoad' => $MaxYearlyLoad,
    //             'Reloadable' => $Reloadable,
    //             'ForEmployee' => $ForEmployee,
    //             'OnlineTransactions' => $OnlineTransactions,
    //             'ATMTransactions' => $ATMTransactions,
    //             'DebitFeePercentage' => $DebitFeePercentage,
    //             'CreditFeePercentage' => $CreditFeePercentage,
    //             'AutoTopupEnabled' => $AutoTopupEnabled,
    //             'AutoTopupDefaultAmount' => $AutoTopupDefaultAmount,
    //             'AutoTopUpTopUpThreshold' => $AutoTopUpTopUpThreshold,
    //             'TokeniseWithoutPaymentCardEnabled' => $TokeniseWithoutPaymentCardEnabled,
    //             'MaxTopupValue' => $MaxTopupValue,
    //             'MinTopupValue' => $MinTopupValue,
    //             'RewardsPointsEnabled' => $RewardsPointsEnabled,
    //             'RewardsPointPerUnit' => $RewardsPointPerUnit,
    //             'AllowAnonymousPinReveal' => $AllowAnonymousPinReveal,
    //             'KycCheck' => $KycCheck,
    //             'PepsSanctionsCheck' => $PepsSanctionsCheck,
    //             'SpendProfileId' => $SpendProfileId,
    //             'SpendProfileName' => $SpendProfileName,
    //             'CreditProfileId' => $CreditProfileId,
    //             'CreditProfileName' => $request->CreditProfileName,
    //             'FundingSource' => $FundingSource,
    //             'TransactionAlerts' => $TransactionAlerts,
    //         ]);
    //     $data = $response->json();
    
    //     // Check the response status code to see if the update was successful
    //     if ($response->ok()) {

    //         $cardproduct = new CardProduct();
    //         $cardproduct->user_id = Auth::user()->id;
    //         $cardproduct->ProductCode = $ProductCode;
    //         $cardproduct->save();
    //         return redirect()->route('userCardService')->with('successmsg', 'Product Create Successfully!');
    //     } else {
    //         return redirect()->back()->with('error', 'Unable to create product.');
    //     }
    // }

    public function applyForCardHolder()
    {
        return view('frontend.user.card.applyforcardholder');
    }

    public function applyForCardHolderStore(Request $request)
    {

        $chknumber =  $request->input('Mobile');
        $num = substr($chknumber, 0, 3);
        if ($num == "+44") {
            $num4 = substr($chknumber, 3, 1);
            if ($num4 == 0) {
                $phone_number = '+44'.substr($request->Mobile, 4);
            }else{
                $phone_number = $request->Mobile;
            }
            
        $FirstName = $request->input('FirstName');
        $LastName = $request->input('LastName');
        // $UserName = $request->input('UserName');
        $UserName = "T".time().Auth::user()->id;
        $SecondSurname = $request->input('SecondSurname');
        $Email = $request->input('Email');
        $Password = "TEVINI@a123";
        $Mobile = $phone_number;
        $LandlineTelephone = $request->input('LandlineTelephone');
        $DateOfBirth = $request->DateOfBirth;
        $SocialSecurityNumber = $request->input('SocialSecurityNumber');
        $IdCardNumber = $request->input('IdCardNumber');

        $Nationality = $request->input('Nationality');
        $Title = $request->input('Title');
        $TaxIdCardNumber = $request->input('TaxIdCardNumber');
        $HouseNumberOrBuilding = $request->input('HouseNumberOrBuilding');
        $Address1 = $request->input('Address1');
        $Address2 = $request->input('Address2');
        $PostCode = $request->input('PostCode');

        $State = $request->input('State');
        $City = $request->input('City');
        $Language = "en-GB";
        $OnfidoId = 1;
        $Country = "GBR";
        $Gender = 1;
        
        // Send a POST request to the API with the updated finance fee value
        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/cardHolder', [
                'FirstName' => $FirstName,
                'LastName' => $LastName,
                'UserName' => $UserName,
                'SecondSurname' => $SecondSurname,
                'Email' => $Email,
                'Password' => $Password,
                'Mobile' => $Mobile,
                'LandlineTelephone' => $LandlineTelephone,
                'Language' => "en-GB",
                'DateOfBirth' => $DateOfBirth,
                'SocialSecurityNumber' => $SocialSecurityNumber,
                'IdCardNumber' => $IdCardNumber,
                'Nationality' => $Nationality,
                'Title' => $Title,
                'TaxIdCardNumber' => $TaxIdCardNumber,
                'HouseNumberOrBuilding' => $HouseNumberOrBuilding,
                'Address1' => $Address1,
                'Address2' => $Address2,
                'PostCode' => $PostCode,
                'City' => $City,
                'State' => $State,
                'Country' => "GBR",
                'OnfidoId' => "1",
                'Gender' => "1"

            ]);

        // Check the API response
        if ($response->successful()) {
            // API request succeeded
            $responseData = $response->json();
            // Process the response data as needed
            

            // return $responseData;
            $cardholder = new CardHolder;
            $cardholder->user_id = Auth::user()->id;
            $cardholder->CardHolderId = $responseData['CardHolderId'];
            $cardholder->FirstName = $FirstName;
            $cardholder->LastName = $LastName;
            $cardholder->UserName = $UserName;
            $cardholder->SecondSurname = $SecondSurname;
            $cardholder->Email = $Email;
            // $cardholder->Password = $Password;
            $cardholder->Mobile = $Mobile;
            $cardholder->LandlineTelephone = $LandlineTelephone;
            $cardholder->DateOfBirth = $DateOfBirth;
            $cardholder->SocialSecurityNumber = $SocialSecurityNumber;
            $cardholder->IdCardNumber = $IdCardNumber;
            $cardholder->Nationality = $Nationality;
            $cardholder->Title = $Title;
            $cardholder->TaxIdCardNumber = $TaxIdCardNumber;
            $cardholder->HouseNumberOrBuilding = $HouseNumberOrBuilding;
            $cardholder->Address1 = $Address1;
            $cardholder->Address2 = $Address2;
            $cardholder->PostCode = $PostCode;
            $cardholder->State = $State;
            $cardholder->City = $City;
            $cardholder->Language = $Language;
            $cardholder->OnfidoId = $OnfidoId;
            $cardholder->Country = $Country;
            $cardholder->Gender = $Gender;
            $cardholder->save();

            // Redirect back with success message and API response data
            return redirect()->route('userCardService')->with('success', 'Card holder information save successful')->with('responseData', $responseData);

        } else {
            // API request failed
            $errorResponse = $response->json();
            // Handle the error response
            // Redirect back with error message and error response data
            return redirect()->back()->with('error', 'API request failed')->with('errorResponse', $errorResponse);
        }
        } else {

            return redirect()->back()->with('error', 'Mobile number start with +44.');
        }
        
    }

    public function updateCardHolder()
    {
        $data = CardHolder::where('user_id', Auth::user()->id)->first();
        return view('frontend.user.card.updatecardholder', compact('data'));
    }

    // update cardholder
    public function updateCardHolderPost(Request $request)
    {
        $chknumber =  $request->input('Mobile');
        $num = substr($chknumber, 0, 3);

        if ($num == "+44") {
            $num4 = substr($chknumber, 3, 1);
            if ($num4 == 0) {
                $phone_number = '+44'.substr($request->Mobile, 4);
            }else{
                $phone_number = $request->Mobile;
            }

            $CardHolderId = $request->input('CardHolderId');
            $FirstName = $request->input('FirstName');
            $LastName = $request->input('LastName');
            $SecondSurname = $request->input('SecondSurname');
            $Email = $request->input('Email');
            $Mobile = $phone_number;
            $LandlineTelephone = $request->input('LandlineTelephone');
            $DateOfBirth = $request->DateOfBirth;
            $SocialSecurityNumber = $request->input('SocialSecurityNumber');
            $IdCardNumber = $request->input('IdCardNumber');
            $Nationality = $request->input('Nationality');
            $Title = $request->input('Title');
            $TaxIdCardNumber = $request->input('TaxIdCardNumber');
            $HouseNumberOrBuilding = $request->input('HouseNumberOrBuilding');
            $Address1 = $request->input('Address1');
            $Address2 = $request->input('Address2');
            $PostCode = $request->input('PostCode');
            $State = $request->input('State');
            $City = $request->input('City');
            $Language = "en-GB";
            $OnfidoId = 1;
            $Country = "GBR";
            $ISOCountryCode = "GBR";
            $Gender = 1;

            // Send a POST request to the API with the updated finance fee value
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/user/cardHolderId/'.$CardHolderId.'', [
                    'AcceptedTAndCs' => true,
                    'ShownAutoCommsOptIn' => true,
                    'PassedSanctions' => true,
                    'PassedPeps' => true,
                    'PassedKyc' => true,
                    'IsActive' => true,
                    'FirstName' => $FirstName,
                    'LastName' => $LastName,
                    'SecondSurname' => $SecondSurname,
                    'EmailAddress' => $Email,
                    'Mobile' => $Mobile,
                    'LandlineTelephone' => $LandlineTelephone,
                    'Language' => "en-GB",
                    'DateOfBirth' => $DateOfBirth,
                    'SocialSecurityNumber' => $SocialSecurityNumber,
                    'IdCardNumber' => $IdCardNumber,
                    'Nationality' => $Nationality,
                    'Title' => $Title,
                    'TaxIdCardNumber' => $TaxIdCardNumber,
                    'HouseNumberOrBuilding' => $HouseNumberOrBuilding,
                    'Address1' => $Address1,
                    'Address2' => $Address2,
                    'PostCode' => $PostCode,
                    'City' => $City,
                    'State' => $State,
                    'Country' => "GBR",
                    'ISOCountryCode' => "GBR",
                    'OnfidoId' => "1",
                    'Gender' => "1"

                ]);

            // Check the API response
            if ($response->successful()) {
                // API request succeeded
                $responseData = $response->json();
                // Process the response data as needed

                // return $responseData;
                $cardholder = CardHolder::find($request->cardid);
                $cardholder->FirstName = $FirstName;
                $cardholder->LastName = $LastName;
                $cardholder->SecondSurname = $SecondSurname;
                $cardholder->Email = $Email;
                $cardholder->Mobile = $Mobile;
                $cardholder->LandlineTelephone = $LandlineTelephone;
                $cardholder->DateOfBirth = $DateOfBirth;
                $cardholder->SocialSecurityNumber = $SocialSecurityNumber;
                $cardholder->IdCardNumber = $IdCardNumber;
                $cardholder->Nationality = $Nationality;
                $cardholder->Title = $Title;
                $cardholder->TaxIdCardNumber = $TaxIdCardNumber;
                $cardholder->HouseNumberOrBuilding = $HouseNumberOrBuilding;
                $cardholder->Address1 = $Address1;
                $cardholder->Address2 = $Address2;
                $cardholder->PostCode = $PostCode;
                $cardholder->State = $State;
                $cardholder->City = $City;
                $cardholder->Language = $Language;
                $cardholder->OnfidoId = $OnfidoId;
                $cardholder->Country = $Country;
                $cardholder->Gender = $Gender;
                $cardholder->save();

                // Redirect back with success message and API response data
                return redirect()->route('userCardService')->with('success', 'Card request update successful')->with('responseData', $responseData);

            } else {
                // API request failed
                $errorResponse = $response->json();
                // Handle the error response
                // Redirect back with error message and error response data
                return redirect()->back()->with('error', 'API request failed')->with('errorResponse', $errorResponse);
            }

        } else {
            return redirect()->back()->with('error', 'Mobile number start with +44.');
        }



    }
    // update cardholder end


    public function orderCard()
    {
        $cardsts = CardStatus::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();
        $chkorder = CardOrder::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();

        if (isset($chkorder)) {
            $ldate = date('Y-m-d H:i:s');
            $updated_at = $chkorder->created_at;
            $updated_at->addDays(12); 
            // dd($cardsts);

            if (empty($cardsts)) {
                if ($ldate < $updated_at) {
                    $CardHolderData = CardHolder::where('user_id', Auth::user()->id)->first();
                    $order = CardOrder::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();
                    return view('frontend.user.card.ordercardcomplete', compact('CardHolderData','order'));
                } else {
                    $CardHolderData = CardHolder::where('user_id', Auth::user()->id)->first();
                    return view('frontend.user.card.ordercard', compact('CardHolderData'));
                }
            } else {
                if ($cardsts->Status == "NORMAL" || $cardsts->Status == "REORDERED" || $cardsts->Status == "ORDERED") {
                    // dd('test2');
                    if ($ldate < $updated_at) {
                        $CardHolderData = CardHolder::where('user_id', Auth::user()->id)->first();
                        $order = CardOrder::where('user_id', Auth::user()->id)->orderby('id', 'DESC')->first();
                        return view('frontend.user.card.ordercardcomplete', compact('CardHolderData','order'));
                    } else {
                        $CardHolderData = CardHolder::where('user_id', Auth::user()->id)->first();
                        return view('frontend.user.card.ordercard', compact('CardHolderData'));
                    }
                } else {
                    // dd('test');
                    $CardHolderData = CardHolder::where('user_id', Auth::user()->id)->first();
                    return view('frontend.user.card.ordercard', compact('CardHolderData'));
                }
            }
            
        } else {
            $CardHolderData = CardHolder::where('user_id', Auth::user()->id)->first();
            return view('frontend.user.card.ordercard', compact('CardHolderData'));
        }
        

    }

    public function orderCardStore(Request $request)
    {
        if (Auth::user()->balance > 5) {
            $chkPrevOrder = CardOrder::where('user_id', Auth::user()->id)->first();
            if ($chkPrevOrder) {
                $chngSts = new CardStatus;
                $chngSts->Status = "REORDERED";
                $chngSts->user_id = Auth::user()->id;
                $chngSts->save();
                
            }else {
                $chngSts = new CardStatus;
                $chngSts->Status = "ORDERED";
                $chngSts->user_id = Auth::user()->id;
                $chngSts->save();
            }

            $ProductCodeId = CardProduct::where('user_id', Auth::user()->id)->first()->ProductCode;
            $ProductCode = $ProductCodeId;
            $FirstName = $request->input('FirstName');
            $LastName = $request->input('LastName');
            $SecondSurname = $request->input('SecondSurname');
            $NameOnCard = $request->input('NameOnCard');
            $CardDesign = "TEVINI";
            $AdditionalCardEmbossData = $request->input('AdditionalCardEmbossData');
            $Title = $request->input('Title');
            $Address1 = $request->input('Address1');
            $Address2 = $request->input('Address2');
            $Address3 = $request->input('Address3');
            $PostCode = $request->input('PostCode');
            $CardholderId = $request->input('CardholderId');
            $State = $request->input('State');
            $City = $request->input('City');
            $ISOCountryCode = "GBR";
            $RecipientEmail = $request->input('RecipientEmail');
            $HouseNumberOrBuilding = $request->input('HouseNumberOrBuilding');
            $Dob = $request->Dob;
            $Language = "en-GB";
            
            // Send a POST request to the API with the updated finance fee value
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/order', [
                    'FirstName' => $FirstName,
                    'LastName' => $LastName,
                    'ProductCode' => $ProductCode,
                    'SecondSurname' => $SecondSurname,
                    'RecipientEmail' => $RecipientEmail,
                    'NameOnCard' => $NameOnCard,
                    'CardDesign' => $CardDesign,
                    'AdditionalCardEmbossData' => $AdditionalCardEmbossData,
                    'Language' => "en-GB",
                    'ISOCountryCode' => $ISOCountryCode,
                    'CardholderId' => $CardholderId,
                    'Dob' => $Dob,
                    'Title' => $Title,
                    'Address1' => $Address1,
                    'Address2' => $Address2,
                    'PostCode' => $PostCode,
                    'Address3' => $Address3,
                    'City' => $City,
                    'State' => $State,
                ]);

            // Check the API response
            if ($response->successful()) {
                // API request succeeded
                $responseData = $response->json();
                // Process the response data as needed
                
                $order = new CardOrder;
                $order->user_id = Auth::user()->id;
                $order->FirstName = $FirstName;
                $order->LastName = $LastName;
                $order->ProductCode = $ProductCode;
                $order->SecondSurname = $SecondSurname;
                $order->RecipientEmail = $RecipientEmail;
                $order->NameOnCard = $NameOnCard;
                $order->CardDesign = $CardDesign;
                $order->AdditionalCardEmbossData = $AdditionalCardEmbossData;
                $order->Language = "en-GB";
                $order->ISOCountryCode = $ISOCountryCode;
                $order->CardholderId = $CardholderId;
                $order->Dob = $Dob;
                $order->Title = $Title;
                $order->Address1 = $Address1;
                $order->Address2 = $Address2;
                $order->Address3 = $Address3;
                $order->PostCode = $PostCode;
                $order->City = $City;
                $order->State = $State;
                $order->HouseNumberOrBuilding = $HouseNumberOrBuilding;
                $order->save();

                
                $user = User::find(Auth::user()->id);
                $user->balance = $user->balance - 5;
                $user->save();

                $utran = new Usertransaction;
                $utran->t_id =  time() . "-" . Auth::user()->id;
                $utran->user_id = Auth::user()->id;
                $utran->t_type = "Out";
                $utran->source = "Tevini Card";
                $utran->amount = "5.00";
                $utran->title = "Tevini Card Order";
                $utran->pending = 1;
                $utran->status = 1;
                $utran->save();

                $chtran = new Transaction();
                $chtran->t_id =  $utran->t_id;
                $chtran->user_id = Auth::user()->id;
                $chtran->t_type = "Out";
                $chtran->name = "Tevini Card";
                $chtran->amount = "5.00";
                $chtran->note = "Tevini Card Order";
                $chtran->status = 1;
                $chtran->save();

                // card balance update
                if (isset(Auth::user()->CreditProfileId)) {
                    $CreditProfileId = Auth::user()->CreditProfileId;
                    $CreditProfileName = Auth::user()->name;
                    $AvailableBalance = 0 - 5;
                    $comment = "Card Order balance";
                    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                            'CreditProfileId' => $CreditProfileId,
                            'CreditProfileName' => $CreditProfileName,
                            'AvailableBalance' => $AvailableBalance,
                            'comment' => $comment,
                        ]);
                }
                // card balance update end
        

                // Redirect back with success message and API response data
                return redirect()->route('userCardService')->with('success', 'Card order request successful')->with('responseData', $responseData);

            } else {
                // API request failed
                $errorResponse = $response->json();
                // Handle the error response
                // Redirect back with error message and error response data
                return redirect()->back()->with('error', 'Card order request failed')->with('errorResponse', $errorResponse);
            }

        } else {
            return redirect()->back()->with('error', 'Minimum £5 required to place a card order');
        }
    }
   

    public function cardActivation(Request $request)
    {
        
        $CardHolderId = CardHolder::where('user_id', Auth::user()->id)->first()->CardHolderId;
        return view('frontend.user.card.cardactivation', compact('CardHolderId'));
    }


    public function cardActivationstore(Request $request)
    {
        $CardHolderId = CardHolder::where('user_id', Auth::user()->id)->first()->CardHolderId;

        $PAN = $request->PAN;
        $CardDisplayName = $request->CardDisplayName;
        $cardNumber = substr($PAN, -4);

        // Send a POST request to the API with the updated finance fee value
        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/cardProxyId', [
                'PAN' => $PAN,
            ]);
    
        // Check the response status code to see if the update was successful
        if ($response->ok()) {
            // apply for product start
            $data = $response->json();
            $CardProxyId = $data['CardProxyId'];

            // Send a POST request to the API with the updated finance fee value
            $productResponse = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/card/activate', [
                
                'CardHolderId' => $CardHolderId,
                'CardProxyId' => $CardProxyId,
                'CardDisplayName' => $CardDisplayName,
                
            ]);
            $responseData = $productResponse->json();
            // Check the response status code to see if the update was successful
            if ($productResponse->ok()) {

                // store card proxi id for showing transaction
                $updateproduct = CardProduct::where('user_id',Auth::user()->id)->first();
                $upPid = CardProduct::find($updateproduct->id);
                $upPid->CardProxyId =  $data['CardProxyId'];
                $upPid->cardNumber =  $cardNumber;
                $upPid->save();

                $changests = new CardStatus();
                $changests->user_id = Auth::user()->id;
                $changests->CardProxyId = $data['CardProxyId'];
                $changests->Status = "NORMAL";
                $changests->save();

                return redirect()->route('userCardService')->with('successmsg', 'Card active Successfully!');
            } else {
                // if ($responseData['ErrorCodes'][0] == "30000026") {
                //     return redirect()->back()->with('error', 'Invalid Card Currency.');
                // } elseif ($responseData['ErrorCodes'][0] == "10000002") {
                //     return redirect()->back()->with('error', 'The card is already in use.');
                // }else {
                //     return redirect()->back()->with('error', 'Expectation Failed.');
                // }

                return redirect()->back()->with('error', $responseData['Message']);
                
            }
        } else {
            return redirect()->back()->with('error', 'Unable to active card.');
        }
    }

    // set card pin
    public function cardSetPin(Request $request, $id)
    {
        $chkid = decrypt($id);
        if ($chkid == Auth::user()->id) {
            
            $proxyid = CardProduct::where('user_id', Auth::user()->id)->first()->CardProxyId;
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/card/pin/cardProxyId/'.$proxyid.'', [
                        'Accept' => 'application/json',
                    ]);
                    
            $alldata = $response->json();
            $pin = $alldata['PIN'];
            $CardHolderId = CardHolder::where('user_id', Auth::user()->id)->first()->CardHolderId;
            

            return view('frontend.user.card.setpin', compact('CardHolderId','pin'));
        } else {
            
            return redirect()->route('userCardService');
        }
        
    }
    
    public function cardSetPinstore(Request $request)
    {

        $PAN = $request->PAN;
        $PIN = $request->PIN;
        // Send a POST request to the API with the updated finance fee value
        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/cardProxyId', [
                'PAN' => $PAN,
            ]);
    
        // Check the response status code to see if the update was successful
        if ($response->ok()) {
            // apply for product start
            $data = $response->json();
            $CardProxyId = $data['CardProxyId'];
            

            // Send a POST request to the API with the updated finance fee value
            $productResponse = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/card/pin/setPin', [
                
                'PIN' => $PIN,
                'CardProxyId' => $CardProxyId,
                
            ]);

            // Check the response status code to see if the update was successful
            if ($productResponse->ok()) {
                return redirect()->route('userCardService')->with('successmsg', 'Card PIN add Successfully!');
            } else {
                return redirect()->back()->with('error', 'Unable to set pin.');
            }
        } else {
            return redirect()->back()->with('error', 'Unable to set pin.');
        }
    }

    public function cardStatusChange(Request $request)
    {
        
        return view('frontend.user.card.status');
    }


    public function cardStatusChangeStore(Request $request)
    {
        $CardProxyId = CardProduct::where('user_id', Auth::user()->id)->first()->CardProxyId;

        $Status = $request->Status;


        // Send a POST request to the API with the updated finance fee value
        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/card/status', [
                'CardProxyId' => $CardProxyId,
                'Status' => $Status,
            ]);
    
        // Check the response status code to see if the update was successful
        if ($response->ok()) {
            // apply for product start

            $changests = new CardStatus();
            $changests->user_id = Auth::user()->id;
            $changests->CardProxyId = $CardProxyId;
            $changests->Status = $Status;
            $changests->save();


            // Check the response status code to see if the update was successful
            return redirect()->route('userCardService')->with('successmsg', 'Card Status Change Successfully!');
        } else {
            return redirect()->back()->with('error', 'Unable to change status.');
        }
    }

    public function mobileVerify(Request $request)
    {



        return view('frontend.user.card.verify');



        // try {
        // }catch (Exception $e) {
        //     return redirect()->route('userCardService')->with('pinerrmsg', 'There is an error to get pin..!');
        // }




        
    }

    public function sendSMS(Request $request)
    {
        
    }


    // return url
    public function authorisation(Request $request)
    {
        if ($request->msgType == 100) {
            $cardNumber = substr($request->PAN, -4);
            $chkuser = CardProduct::where('cardNumber', $cardNumber)->first();

            $DateTime = now();
            $data = new Authorisation();
            if (isset($chkuser)) {
                $data->user_id = $chkuser->user_id;
            }

            $data->Utid = $request->Utid;
            $data->messageID = $request->messageID;
            $data->instCode = $request->instCode;
            $data->txnType = $request->txnType;
            $data->msgType = $request->msgType;
            $data->tlogId = $request->tlogId;
            $data->orgTlogID = $request->orgTlogID;
            $data->timeout = $request->timeout;
            $data->repeat = $request->repeat;
            $data->PAN = $request->PAN;
            $data->cardID = $request->cardID;
            $data->accNo = $request->accNo;
            $data->curBill = $request->curBill;
            $data->avlBal = $request->avlBal;
            $data->blkAmt = $request->blkAmt;
            $data->localDate = $request->localDate;
            $data->localTime = $request->localTime;
            $data->amtTxn = $request->amtTxn;
            $data->curTxn = $request->curTxn;
            $data->billAmt = $request->billAmt;
            $data->billConvRate = $request->billConvRate;
            $data->amtCom = $request->amtCom;
            $data->amtPad = $request->amtPad;
            $data->txnCode = $request->txnCode;
            $data->termCode = $request->termCode;
            $data->crdAcptID = $request->crdAcptID;
            $data->crdAcptLoc = $request->crdAcptLoc;
            $data->MCC = $request->MCC;
            $data->poschp = $request->poschp;
            $data->poscdim = $request->poscdim;
            $data->poscham = $request->poscham;
            $data->poscp = $request->poscp;
            $data->approvalCode = $request->approvalCode;
            $data->sysDate = $request->sysDate;
            $data->rev = $request->rev;
            $data->orgItemId = $request->orgItemId;
            $data->itemSrc = $request->itemSrc;
            $data->amtFee = $request->amtFee;
            $data->crdproduct = $request->crdproduct;
            $data->ctxLocalDate = $request->ctxLocalDate;
            $data->ctxLocalTime = $request->ctxLocalTime;
            $data->aVSChkRs = $request->aVSChkRs;
            $data->threeDSecChkRs = $request->threeDSecChkRs;
            $data->actionCode = $request->actionCode;
            $data->amtCashback = $request->amtCashback;
            $data->trn = $request->trn;
            $data->txnSubCode = $request->txnSubCode;
            if ($data->save()) {

                if ($request->actionCode == "000") {
                    if (isset($chkuser)) {
                        $user = User::find($chkuser->user_id);
                        $user->balance = $user->balance - $request->billAmt;
                        $user->save();
    
    
                        $utran = new Usertransaction;
                        $utran->t_id =  time() . "-" . $chkuser->user_id;
                        $utran->user_id = $chkuser->user_id;
                        $utran->t_type = "Out";
                        $utran->source = "Tevini Card";
                        $utran->amount = $request->billAmt;
                        $utran->crdAcptLoc = $request->crdAcptLoc;
                        $utran->crdAcptID = $request->crdAcptID;
                        $utran->title = "Tevini Card Payment";
                        $utran->pending = 1;
                        $utran->status = 1;
                        $utran->save();
    
                        $chtran = new Transaction();
                        $chtran->t_id =  $utran->t_id;
                        $chtran->user_id = $chkuser->user_id;
                        $chtran->t_type = "Out";
                        $chtran->name = "Tevini Card";
                        $chtran->amount = $request->billAmt;
                        $chtran->crdAcptLoc = $request->crdAcptLoc;
                        $chtran->crdAcptID = $request->crdAcptID;
                        $chtran->note = "Tevini Card Payment";
                        $chtran->status = 1;
                        $chtran->save();
                    }
                }

                

                // Send a POST request to the API with the updated finance fee value
                $Response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/redundantposts', [
                    
                    'Type' => "AUTH",
                    'DateTime' => $DateTime,
                    
                ]);
            } else {
                return redirect()->back()->with('error', 'Authorization error.');
            }
        } else {
            return redirect()->back()->with('error', 'Authorization error.');
        }
        
            
            
    }

    public function settlement(Request $request)
    {
        $cardNumber = substr($request->PAN, -4);
        $chkuser = CardProduct::where('cardNumber', $cardNumber)->first();

        $DateTime = now();
        $data = new Settlement();
        if (isset($chkuser)) {
            $data->user_id = $chkuser->user_id;
        }
        $data->MTID = $request->MTID;
        $data->localDate = $request->localDate;
        $data->localTime = $request->localTime;
        $data->tlogId = $request->tlogId;
        $data->orgTlogID = $request->orgTlogID;
        $data->orgItemId = $request->orgItemId;
        $data->PAN = $request->PAN;
        $data->cardID = $request->cardID;
        $data->txnCode = $request->txnCode;
        $data->txnSubCode = $request->txnSubCode;
        $data->amtFee = $request->amtFee;
        $data->curTxn = $request->curTxn;
        $data->amtTxn = $request->amtTxn;
        $data->amtCashback = $request->amtCashback;
        $data->billAmt = $request->billAmt;
        $data->curBill = $request->curBill;
        $data->billConvRate = $request->billConvRate;
        $data->CURSET = $request->CURSET;
        $data->AMTSET = $request->AMTSET;
        $data->RATESET = $request->RATESET;
        $data->RATESETECB = $request->RATESETECB;
        $data->approvalCode = $request->approvalCode;
        $data->crdAcptID = $request->crdAcptID;
        $data->crdAcptLoc = $request->crdAcptLoc;
        $data->termCode = $request->termCode;
        $data->MCC = $request->MCC;
        $data->ctxLocalDate = $request->ctxLocalDate;
        $data->ctxLocalTime = $request->ctxLocalTime;
        $data->PRODCODE = $request->PRODCODE;
        $data->trn = $request->trn;
        $data->poschp = $request->poschp;
        $data->poscp = $request->poscp;
        $data->poscdim = $request->poscdim;
        $data->poscham = $request->poscham;
        $data->settlementActionCode = $request->settlementActionCode;
        $data->Utid = $request->Utid;
        if ($data->save()) {

            if (isset($chkuser)) {
                // $user = User::find($chkuser->user_id);
                // $user->balance = $user->balance - $request->billAmt;
                // $user->save();
            }


            // Send a POST request to the API with the updated finance fee value
            $Response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/redundantposts', [
                
                'Type' => "SETTLEMENT",
                'DateTime' => $DateTime,
                
            ]);
        } else {
            return redirect()->back()->with('error', 'SETTLEMENT ERROR.');
        }
        
        
    }

    public function expired(Request $request)
    {

        
        $cardNumber = substr($request->PAN, -4);
        $chkuser = CardProduct::where('cardNumber', $cardNumber)->first();
        $DateTime = now();

        $data = new Expired();
        if (isset($chkuser)) {
            $data->user_id = $chkuser->user_id;
        }
        $data->Utid = $request->Utid;
        $data->messageID = $request->messageID;
        $data->instCode = $request->instCode;
        $data->txnType = $request->txnType;
        $data->msgType = $request->msgType;
        $data->tlogId = $request->tlogId;
        $data->orgTlogID = $request->orgTlogID;
        $data->timeout = $request->timeout;
        $data->repeat = $request->repeat;
        $data->PAN = $request->PAN;
        $data->cardID = $request->cardID;
        $data->accNo = $request->accNo;
        $data->curBill = $request->curBill;
        $data->avlBal = $request->avlBal;
        $data->blkAmt = $request->blkAmt;
        $data->localDate = $request->localDate;
        $data->localTime = $request->localTime;
        $data->amtTxn = $request->amtTxn;
        $data->curTxn = $request->curTxn;
        $data->billAmt = $request->billAmt;
        $data->billConvRate = $request->billConvRate;
        $data->amtCom = $request->amtCom;
        $data->amtPad = $request->amtPad;
        $data->txnCode = $request->txnCode;
        $data->termCode = $request->termCode;
        $data->crdAcptID = $request->crdAcptID;
        $data->crdAcptLoc = $request->crdAcptLoc;
        $data->MCC = $request->MCC;
        $data->poschp = $request->poschp;
        $data->poscdim = $request->poscdim;
        $data->poscham = $request->poscham;
        $data->poscp = $request->poscp;
        $data->approvalCode = $request->approvalCode;
        $data->sysDate = $request->sysDate;
        $data->rev = $request->rev;
        $data->orgItemId = $request->orgItemId;
        $data->itemSrc = $request->itemSrc;
        $data->amtFee = $request->amtFee;
        $data->crdproduct = $request->crdproduct;
        $data->ctxLocalDate = $request->ctxLocalDate;
        $data->ctxLocalTime = $request->ctxLocalTime;
        $data->aVSChkRs = $request->aVSChkRs;
        $data->threeDSecChkRs = $request->threeDSecChkRs;
        $data->actionCode = $request->actionCode;
        $data->amtCashback = $request->amtCashback;
        $data->trn = $request->trn;
        $data->txnSubCode = $request->txnSubCode;
        if ($data->save()) {

            if (isset($chkuser)) {
                // $user = User::find($chkuser->user_id);
                // $user->balance = $user->balance + $request->billAmt;
                // $user->save();
            }

            // Send a POST request to the API with the updated finance fee value
            $Response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/redundantposts', [
                
                'Type' => "EXPIRED",
                'DateTime' => $DateTime,
                
            ]);
        } else {
            return redirect()->back()->with('error', 'EXPIRED ERROR.');
        }
        
        
        
    }

}
