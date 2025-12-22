<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorisation;
use App\Models\CardProduct;
use App\Models\Expired;
use App\Models\Settlement;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/product/portalproducts/', [
                    'Accept' => 'application/json',
                ]);
            
            $data = $response->json();
            
            return view('product.index', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    public function getAuthorisation(Request $request)
    {
        $data = Authorisation::orderby('id', 'DESC')->get();
        return view('cardservice.authorisation', compact('data'));
    }

    public function getAuthorisationDetails($id)
    {
        $data = Authorisation::where('id', $id)->first();
        return view('cardservice.authorisationDetails', compact('data'));
    }

    public function getSettlement(Request $request)
    {
        $data = Settlement::orderby('id', 'DESC')->get();
        return view('cardservice.settlement', compact('data'));
    }

    public function getExpired(Request $request)
    {
        $data = Expired::orderby('id', 'DESC')->get();
        return view('cardservice.expired', compact('data'));
    }

    public function getExpiredDetails($id)
    {
        $data = Expired::where('id', $id)->first();
        return view('cardservice.expiredDetails', compact('data'));
    }

    public function getCardTransaction(Request $request)
    {
        $data = CardProduct::whereNotNull('CardProxyId')->latest()->get();
        return view('cardservice.carduser', compact('data'));
    }

    public function getUserCardTransaction(Request $request)
    {
        $cardProxyId = $request->CardProxyId;
        $startDate = $request->fromDate;
        $endDate = $request->endDate;

        
        try {
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->get('https://tevini.api.qcs-uk.com/api/cardService/v1/transaction/cardProxyId/'.$cardProxyId.'/startDate/'.$startDate.'/endDate/'.$endDate.'', [
                    'Accept' => 'application/json',
                ]);

            $data = $response->json();
            
            return view('cardservice.transaction', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }

    
}
