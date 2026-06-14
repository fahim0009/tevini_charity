<?php

namespace App\Http\Controllers;

use App\Models\VoucherCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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




}
