<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TdfTransaction;
use Illuminate\Http\Request;

class TDFTransactionController extends Controller
{
    public function getTDFTransaction(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->get();
        return view('admin.tdf.transaction', compact('data'));
    }

    public function tdfBlanceStore(Request $request)
    {
        
        $data = TdfTransaction::findOrFail($request->tdfid);
        $data->payment_date = date('Y-m-d');
        $data->payment_dollar_amount = $request->amount;
        $data->status = "1";
        if($data->save()){

            $message ="Data Update Successfully";

        return redirect()->route('tdfTransaction')->with(['status'=> 303,'message'=> $message]);
        }
        return back()->with(['status'=> 303,'message'=>'Server Error!!']);


    }
}
