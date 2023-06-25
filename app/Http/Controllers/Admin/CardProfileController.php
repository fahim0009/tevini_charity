<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CardProfileController extends Controller
{
    public function cardprofile(Request $request)
    {

        try {
            $data = User::whereNotNull('CreditProfileId')->get();
            // return $data;
            return view('cardservice.creditProfiles', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    public function cardprofileview(Request $request, $id)
    {

        try {
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/creditProfile/'.$id.'', [
                    'Accept' => 'application/json',
                ]);
            
            $data = $response->json();
            return view('cardservice.view', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    public function cardprofileEdit(Request $request, $id)
    {

        try {
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/creditProfile/'.$id.'', [
                    'Accept' => 'application/json',
                ]);
            
            $data = $response->json();
            return view('cardservice.edit', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    public function productfeeUpdate(Request $request)
    {
        $CreditProfileId = $request->CreditProfileId;
        $CreditProfileName = $request->input('CreditProfileName');
        $AvailableBalance = $request->input('AvailableBalance');
        $comment = $request->input('comment');
        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                'CreditProfileId' => $CreditProfileId,
                'CreditProfileName' => $CreditProfileName,
                'AvailableBalance' => $AvailableBalance,
                'comment' => $comment,
            ]);
            if ($response->ok()) {
            return redirect()->back()->with('success', 'Available Balance updated successfully!');
        } else {
            return redirect()->back()->with('error', 'New balance greater then Credit Limit.');
        }
    }

    public function cardprofileLimite(Request $request, $id)
    {

        try {
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/creditProfile/'.$id.'', [
                    'Accept' => 'application/json',
                ]);
            
            $data = $response->json();
            return view('cardservice.limitUpdate', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    public function productfeeLimiteUpdate(Request $request)
    {
        $CreditProfileId = $request->CreditProfileId;
        $CreditProfileName = $request->input('CreditProfileName');
        $CreditLimit = $request->input('CreditLimit');
        $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/creditLimit', [
                'CreditProfileId' => $CreditProfileId,
                'CreditProfileName' => $CreditProfileName,
                'CreditLimit' => $CreditLimit,
            ]);
            if ($response->ok()) {
            return redirect()->back()->with('success', 'Balance Limit updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Something Went wrong.');
        }
    }


}
