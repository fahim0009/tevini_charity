<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Usertransaction;
use Illuminate\Support\Facades\DB;

class DonorBalanceController extends Controller
{
    public function index(Request $request)
    {

        $alltransactions = Transaction::orderBy('id','DESC')->limit(500)->get();


        $data = DB::table('usertransactions')
                        ->select('user_id', DB::raw('SUM(CASE WHEN t_type = "In"  AND status = 1 AND commission != 0 THEN amount  WHEN t_type = "In"  AND status = 1 AND commission = 0 THEN amount  WHEN t_type = "Out" AND (status = 1 OR pending != 0) THEN -amount END) AS total_balance'))
                        ->groupBy('user_id')
                        ->get();


        // dd($data);
        return view('donorbalance.index')
        ->with('data',$data);
    }

    public function balanceUpdate(Request $request)
    {
        
            $active = User::find($request->id);
            $active->balance = $request->balance;
            $active->save();
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Balance Updated Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        

    }
}
