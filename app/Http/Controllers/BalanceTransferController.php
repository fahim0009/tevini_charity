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
            return redirect()->back()->with('success', 'Balance transferred successfully.');
        } else {
            return redirect()->back()->with('error', 'Server Error!!.');
        }
        

    }

}
