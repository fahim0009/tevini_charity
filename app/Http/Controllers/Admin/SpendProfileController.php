<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class SpendProfileController extends Controller
{
    public function index(Request $request)
    {

        try {
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/spendProfiles', [
                    'Accept' => 'application/json',
                ]);
            
            $data = $response->json();
            // return $response;
            return view('cardservice.spendprofile', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
    $MerchantWhiteListId = "1";
    $MerchantBlackListId = "0";
    
    // Send a POST request to the API with the updated finance fee value
    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/spendProfile', [
            'ProfileName' => $request->ProfileName,
            'MaxSingleTxnValue' => $request->MaxSingleTxnValue,
            'MaxDailyTxnTotalValue' => $request->MaxDailyTxnTotalValue,
            'MaxDailyTotalTxns' => $request->MaxDailyTotalTxns,
            'MinSingleTxnValue' => $request->MinSingleTxnValue,
            'MaxSpend4Days' => $request->MaxSpend4Days,

            'MaxWeeklyTxnValue' => $request->MaxWeeklyTxnValue,
            'MaxMonthlyTxnValue' => $request->MaxMonthlyTxnValue,
            'DailyVelocity' => $request->DailyVelocity,
            'WeeklyVelocity' => $request->WeeklyVelocity,
            'MonthlyVelocity' => $request->MonthlyVelocity,
            'MerchantWhiteListId' => $MerchantWhiteListId,
            'MerchantBlackListId' => $MerchantBlackListId,
        ]);
    
        // Check the response status code to see if the update was successful
        if ($response->ok()) {
            return redirect()->back()->with('success', 'Product fee updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Unable to update product fee.');
        }
    }
}
