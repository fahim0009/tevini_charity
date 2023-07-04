<?php

namespace App\Http\Controllers;

use App\Models\CardHolder;
use App\Models\CardProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        $CreditLimit =  Auth::user()->balance;
        $IsPrePaid = true;

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
            // apply for product start

            $spendProfile = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/spendProfile/2', [
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
            $CreditProfileId = Auth::user()->CreditProfileId;
            $CreditProfileName = Auth::user()->name;
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

        $FirstName = $request->input('FirstName');
        $LastName = $request->input('LastName');
        $UserName = $request->input('UserName');
        $SecondSurname = $request->input('SecondSurname');
        $Email = $request->input('Email');
        $Password = $request->input('Password');
        $Mobile = $request->input('Mobile');
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
        $Country = "UK";
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
                'Country' => "UK",
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
            $cardholder->Password = $Password;
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
            return redirect()->route('userCardService')->with('success', 'Card request successful')->with('responseData', $responseData);

        } else {
            // API request failed
            $errorResponse = $response->json();
            // Handle the error response
            // Redirect back with error message and error response data
            return redirect()->back()->with('error', 'API request failed')->with('errorResponse', $errorResponse);
        }
    }


    public function orderCard()
    {
        $CardHolderData = CardHolder::where('user_id', Auth::user()->id)->first();
        // dd($CardHolderData);
        return view('frontend.user.card.ordercard', compact('CardHolderData'));
    }

    public function orderCardStore(Request $request)
    {

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
    

            // Redirect back with success message and API response data
            return redirect()->route('userCardService')->with('success', 'Card order request successful')->with('responseData', $responseData);

        } else {
            // API request failed
            $errorResponse = $response->json();
            // Handle the error response
            return $errorResponse;
            // Redirect back with error message and error response data
            return redirect()->back()->with('error', 'API request failed')->with('errorResponse', $errorResponse);
        }
    }
   

    public function cardActivation(Request $request)
    {
        $ProfileName = Auth::user()->name;
        $CreditLimit =  Auth::user()->balance;
        $IsPrePaid = true;

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
            return redirect()->back()->with('success', 'Credit Profile Request Create Successfully!');
        } else {
            return redirect()->back()->with('error', 'Unable to update credit profile.');
        }
    }
    




}
