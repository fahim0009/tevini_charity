<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;

class VoucherBookController extends Controller
{
    public function userOrderVoucherBook()
    {
        $data = Voucher::where('status','=','1')->get();
        
        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }
}
