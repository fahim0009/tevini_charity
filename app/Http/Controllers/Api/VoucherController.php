<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provoucher;
use Illuminate\support\Facades\Auth;

class VoucherController extends Controller
{
    public function processed_Voucher_show()
    {
        $data = Provoucher::where([
            ['user_id','=', auth()->user()->id],
            ['waiting','=', 'Yes']
            ])->orderBy('id','DESC')->get();

        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }
}
