<?php

namespace App\Http\Controllers;

use App\Models\Provoucher;
use Illuminate\Http\Request;

class ExpiredVoucherController extends Controller
{
    public function getExpiredVoucher()
    {
        $data = Provoucher::where('expired','=', 'Yes')->orderBy('id','DESC')->get();
        return view('voucher.expiredVoucher', compact('data'));

    }
}
