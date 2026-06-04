<?php

namespace App\Http\Controllers;

use App\Models\VoucherCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    
    public function onlineDonation()
    {
        return view('frontend.onlineDonation');
    }

    public function onlineDonationStore(Request $request)
    {
        dd('Work in progress ...');
        
    }

    public function orderVoucherBooks()
    {
        $cart = auth()->check() 
            ? VoucherCart::where('user_id', Auth::user()->id)->get() 
            : collect();
        
        return view('frontend.voucherBook', compact('cart'));
    }


}
