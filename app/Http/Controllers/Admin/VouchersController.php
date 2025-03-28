<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Provoucher;
use App\Models\Usertransaction;
use Illuminate\Http\Request;

class VouchersController extends Controller
{
    public function getVoucher(Request $request)
    {

        
        if ($request->isMethod('post')) {

            $request->validate([
                'voucher_number' => 'required|string|max:255',
            ]);
    

            $vnumber = $request->voucher_number;
            // $chkVoucher = Usertransaction::where('cheque_no', $vnumber)->whereNotNull('cheque_no')->get();
            // if ($chkVoucher->count() < 1) {
            //     $chkVoucher = Barcode::where('barcode', $vnumber)->get();
                
            // }

            $chkBarcode = Barcode::where('barcode', $vnumber)->get();

            $chkVoucher = Provoucher::where([
                ['cheque_no', '=', $vnumber]
            ])->get();


            if ($chkVoucher->count() > 0) {
                return view('voucher.search', compact('chkVoucher', 'vnumber'))->with('success', 'Voucher found successfully.');
            } elseif ($chkBarcode->count() > 0) {
                $chkVoucher = Barcode::where('barcode', $vnumber)->get();
                return view('voucher.search', compact('chkVoucher', 'vnumber'))->with('success', 'Voucher found successfully.');
            } else {
                
                return redirect()->back()->with(['error' => 'Voucher not found.']);
            }
            
        }else{
            return view('voucher.search');
        }
    }
}
