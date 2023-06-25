<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class ProductFeeController extends Controller
{
    public function index(Request $request)
    {

        try {
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/productfees', [
                    'Accept' => 'application/json',
                ]);
            
            $data = $response->json();
            // return $data;
            return view('productfee.index', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
    $Name = $request->input('Name');
    $FinanceFee = $request->FinanceFee;
    $CardFee = $request->input('CardFee');
    $CardIsueeFee = $request->input('CardIsueeFee');
    $FXFee = $request->input('FXFee');
    
    // Send a POST request to the API with the updated finance fee value
    $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
        ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/productfee', [
            'Name' => $Name,
            'FinanceFee' => $request->FinanceFee,
            'CardFee' => $request->CardFee,
            'CardIsueeFee' => $request->CardIsueeFee,
            'FXFee' => $request->FXFee,
        ]);
    
        // Check the response status code to see if the update was successful
        if ($response->ok()) {
            return redirect()->back()->with('success', 'Product fee updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Unable to update product fee.');
        }
    }
}
