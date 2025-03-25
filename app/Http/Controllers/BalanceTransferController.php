<?php

namespace App\Http\Controllers;

use App\Models\BalanceTransfer;
use App\Models\User;
use App\Models\Usertransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class BalanceTransferController extends Controller
{
    public function balanceTransfer()
    {
        $data = BalanceTransfer::where('transfer_from', Auth::user()->id)->orderBy('id','DESC')->get();
        $receive = BalanceTransfer::where('transfer_to', Auth::user()->id)->orderBy('id','DESC')->get();
        // dd($data);
        return view('frontend.user.transferbalance', compact('data', 'receive'));

    }

    public function balanceTransferStore(Request $request)
    {

        // user balance calculation start
        $gettrans = Usertransaction::where([
            ['user_id','=', auth()->user()->id],
            ['status','=', '1']
        ])->orwhere([
            ['user_id','=', auth()->user()->id],
            ['pending','=', '1']
        ])->orderBy('id','DESC')->get();

        $donorUpBalance = 0;

        foreach ($gettrans as $tran) {
            $donorUpBalance += ($tran->t_type == "In") ? $tran->amount : (($tran->t_type == "Out") ? -$tran->amount : 0);
        }
        // user balance calculation end


        $request->validate([
            'accountno' => 'required|integer|exists:users,accountno',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($request->amount > $donorUpBalance) {
            return redirect()->back()->with('error', 'Insufficient balance.');
        }
        

        $transferTo = User::where('accountno', $request->accountno)->first();

        $balanceTransfer = new BalanceTransfer();
        $balanceTransfer->date = date('Y-m-d');
        $balanceTransfer->transfer_from = Auth::user()->id;
        $balanceTransfer->transfer_to = $transferTo->id;
        $balanceTransfer->accountno = $request->accountno;
        $balanceTransfer->amount = $request->amount;
        $balanceTransfer->status =  1;
        if ($balanceTransfer->save()) {

            $user = User::find(auth()->user()->id);
            $user->balance = $user->balance - $request->amount;
            $user->save();

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $request->amount;
                $comment = "Balance Transfer";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end


            // send
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".Auth::user()->id;
            $udtransaction->user_id = Auth::user()->id;
            $udtransaction->t_type = "Out";
            $udtransaction->amount =  $request->amount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title ="Balance transfer send";
            $udtransaction->status =  1;
            $udtransaction->save();

            // receive
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".$transferTo->id;
            $udtransaction->user_id = $transferTo->id;
            $udtransaction->t_type = "In";
            $udtransaction->amount =  $request->amount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title ="Balance transfer received";
            $udtransaction->status =  1;
            $udtransaction->save();


            return redirect()->back()->with('success', 'Balance transferred successfully.');
        } else {
            return redirect()->back()->with('error', 'Server Error!!.');
        }
        

    }

    public function getBalanceTransferByAdmin()
    {
        $pending = BalanceTransfer::where('status', 0)->orderBy('id','DESC')->get();
        $complete = BalanceTransfer::where('status', 1)->orderBy('id','DESC')->get();
        $cancel = BalanceTransfer::where('status', 2)->orderBy('id','DESC')->get();
        
        return view('admin.balanceTransfer.index', compact('pending', 'complete','cancel'));

    }

    public function changeStatus(Request $request)
    {
        $transfer_to = BalanceTransfer::where('id',$request->id)->first()->transfer_to;
        $transfer_from = BalanceTransfer::where('id',$request->id)->first()->transfer_from;
        $amount = BalanceTransfer::where('id',$request->id)->first()->amount;

        $data = BalanceTransfer::find($request->id);
        $data->status = $request->status;
        $data->save();

        if($request->status == "2"){

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $amount;
                $comment = "Balance Transfer Cancel";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);

        } elseif ($request->status == "1") {

            // send
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".$transfer_from;
            $udtransaction->user_id = $transfer_from;
            $udtransaction->t_type = "Out";
            $udtransaction->amount =  $amount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title ="Balance transfer send";
            $udtransaction->status =  1;
            $udtransaction->save();

            // receive
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".$transfer_to;
            $udtransaction->user_id = $transfer_to;
            $udtransaction->t_type = "In";
            $udtransaction->amount =  $amount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title ="Balance transfer received";
            $udtransaction->status =  1;
            $udtransaction->save();

            
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);

        }else {
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Server error!!.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }


    public function getDonorBalanceTransferByAdmin($id)
    {
        
        $pending = BalanceTransfer::where('status', 0)->where('transfer_from', $id)->orderBy('id','DESC')->get();
        $send = BalanceTransfer::where('status', 1)->where('transfer_from', $id)->orderBy('id','DESC')->get();
        $receive = BalanceTransfer::where('status', 1)->where('transfer_to', $id)->orderBy('id','DESC')->get();
        
        return view('admin.balanceTransfer.donorTran', compact('send', 'receive','pending'));

    }
}
