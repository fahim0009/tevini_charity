<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Auth;

class OrderController extends Controller
{
    public function userOrderview()
    {
        $data = Order::with('orderhistories','orderhistories.voucher')->where('user_id', Auth::user()->id)->orderBy('id','DESC')->get();
        
        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }
}
