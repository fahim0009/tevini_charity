<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Order;
use App\Models\OrderHistory;
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

    public function getBarcode(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'from_barcode_number' => 'required|string|max:255',
                'to_barcode_number' => 'required|string|max:255',
            ]);
    

            $from_barcode_number = $request->from_barcode_number;
            $to_barcode_number = $request->to_barcode_number;

            // We use whereRaw to cast the string column to an unsigned integer
            $chkBarcode = Barcode::whereRaw('CAST(barcode AS UNSIGNED) BETWEEN ? AND ?', [
                $from_barcode_number, 
                $to_barcode_number
            ])->get();



            if ($chkBarcode->count() > 0) {
                
                return view('voucher.deletebarcode', compact('chkBarcode','from_barcode_number', 'to_barcode_number'))->with('success', 'Barcode found successfully.');
            } else {
                
                return redirect()->back()->with(['error' => 'Voucher not found.']);
            }
            
        }else{
            return view('voucher.deletebarcode');
        }
        
    }

    public function deleteBarcode(Request $request)
    {
        $request->validate([
            'from_barcode_number' => 'required|string|max:255',
            'to_barcode_number' => 'required|string|max:255',
        ]);

        $from = $request->from_barcode_number;
        $to = $request->to_barcode_number;

        $barcodes = Barcode::whereBetween('barcode', [$from, $to]);

        if ($barcodes->count() > 0) {
            $deleted = $barcodes->delete();
            return response()->json([
                'success' => true,
                'deleted' => $deleted,
                'message' => 'Barcodes deleted successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No barcodes found to delete.'
            ]);
        }
    }



    public function checkOrder()
    {
        $orders = Order::with('orderhistories')
            ->whereHas('orderhistories', function ($query) {
                $query->where('voucher_id', 20);
            })
            ->get();

            $history = OrderHistory::with('order','order.user')->where('voucher_id', 20)->get();

        return $orders;
    }



}
