<?php

namespace App\Http\Controllers\Api;

use App\Models\BalanceTransfer;
use App\Models\User;
use App\Models\Usertransaction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class FundTransferController extends Controller
{

    public function balanceTransfer()
    {
        $send = BalanceTransfer::where('transfer_from', Auth::user()->id)->orderBy('id','DESC')->get();
        $receive = BalanceTransfer::where('transfer_to', Auth::user()->id)->orderBy('id','DESC')->get();

        $success['send'] = $send;
        $success['receive'] = $receive;
        return response()->json(['success'=>true,'response'=> $success], 200);
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
        if ($request->amount > $donorUpBalance) {
            $success['message'] = 'Insufficient balance.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

        if (empty($request->accountno)) {
            $success['message'] = 'Account Number field requried.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

        if (empty($request->amount)) {
            $success['message'] = 'Amount field requried.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

        if ($request->accountno == Auth::user()->accountno) {
            $success['message'] = 'You cannot transfer to your own account.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }
        if ($request->amount < 1) {
            $success['message'] = 'The transfer amount must be at least 0.01.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

        $chkuser = User::where('accountno', $request->accountno)->first();
        if (empty($chkuser)) {
            $success['message'] = 'Account number not found.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
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
            $udtransaction->title ="Funds send";
            $udtransaction->status =  1;
            $udtransaction->save();

            // receive
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".$transferTo->id;
            $udtransaction->user_id = $transferTo->id;
            $udtransaction->t_type = "In";
            $udtransaction->amount =  $request->amount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title = "Funds received";
            $udtransaction->status =  1;
            $udtransaction->save();


            $success['message'] = 'TDF transferred successfully.';
            $success['data'] = $balanceTransfer;
            $success['balance'] = number_format($donorUpBalance - $request->amount, 2, '.', '');
            return response()->json(['success'=>true,'response'=> $success], 200);


        } else {
            return response()->json(['status'=> 303,'message'=>'Server error!!']);
        }
        

    }
}
