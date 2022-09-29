<?php


namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\OrderHistory;
use App\Models\Commission;
use App\Models\User;
use App\Models\Order;
use App\Models\Donation;
use App\Models\Provoucher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $donation = Donation::where('status','=','1')->sum('amount');
        $transaction = Transaction::whereNotNull('charity_id')->where('t_type','=', 'out')->sum('amount');
        $voucherout = Provoucher::sum('amount');
        $commission = Commission::sum('commission');
        $processvoucher = Provoucher::count();
        return view('admin.dashboard',compact('donation','transaction','voucherout','commission','processvoucher'));
    }

    public function donorNoti(Request $request)
    {

        $data = User::find($request->donorid);
        $data->notification = 0;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Notification Delete Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
        }
        return response()->json(['status'=> 300,'message'=>'Server Error!!']);

    }

    public function orderNoti(Request $request)
    {

        $data = Order::find($request->orderid);
        $data->notification = 0;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Notification Delete Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
        }
        return response()->json(['status'=> 300,'message'=>'Server Error!!']);

    }

    public function donationNoti(Request $request)
    {

        $data = Donation::find($request->donationid);
        $data->notification = 0;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Notification Delete Successfully.</b></div>";

        return response()->json(['status'=> 300,'message'=>$message]);
        }
        return response()->json(['status'=> 300,'message'=>'Server Error!!']);

    }


}
