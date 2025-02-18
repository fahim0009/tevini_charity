<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Usertransaction;
use Illuminate\Http\Request;

class VouchersController extends Controller
{
    public function getVoucher(Request $request)
    {

        $vnumber = $request->voucher_number;
        $chkVoucher = Usertransaction::where('cheque_no', $vnumber)->whereNotNull('cheque_no')->get();
        if ($chkVoucher->count() < 1) {
            $chkVoucher = Barcode::where('barcode', $vnumber)->get();
            
        }

        // dd($chkVoucher);
        return view('voucher.search', compact('chkVoucher','vnumber'));
    }
}
