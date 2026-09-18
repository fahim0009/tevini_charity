<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barcode;
use App\Models\Charity;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Provoucher;
use App\Models\User;
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

    public function deleteVoucher(Request $request)
    {
        $request->validate(['cheque_no' => 'required|string|max:255']);
        $chequeNo = $request->cheque_no;

        \DB::beginTransaction();
        try {
            $vouchers = Provoucher::where('cheque_no', $chequeNo)->get();

            if ($vouchers->isEmpty()) {
                return $this->sendDeleteResponse($request, false, 'Voucher not found in Provoucher table.');
            }

            foreach ($vouchers as $voucher) {
                if ($voucher->tran_id) {
                    Usertransaction::where('id', $voucher->tran_id)->delete();
                }
                Usertransaction::where('cheque_no', $chequeNo)->delete();

                // if ($voucher->status == 1) {
                //     Charity::where('id', $voucher->charity_id)->decrement('balance', $voucher->amount);
                //     if ($voucher->user_id) {
                //         User::where('id', $voucher->user_id)->increment('balance', $voucher->amount);
                //     }
                // }
                $voucher->delete();
            }

            \DB::commit();
            return $this->sendDeleteResponse($request, true, 'Voucher and related transaction deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Voucher deletion failed: ' . $e->getMessage());
            return $this->sendDeleteResponse($request, false, 'Error: ' . $e->getMessage());
        }
    }

    private function sendDeleteResponse($request, $success, $message)
    {
        if ($request->ajax()) {
            return response()->json([
                'status'  => $success ? 'success' : 'error',
                'message' => $message,
            ]);
        }
        return redirect()->route('getVoucher')->with($success ? 'success' : 'error', $message);
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
