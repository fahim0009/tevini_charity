<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            // return $data;
            // dd($data);
            return view('product.index', compact('data'));
        } catch (\Exception $e) {
            // Handle exception
            return view('error', ['message' => $e->getMessage()]);
        }
    }
}
