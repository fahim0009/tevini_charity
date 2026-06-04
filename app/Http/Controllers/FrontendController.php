<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
