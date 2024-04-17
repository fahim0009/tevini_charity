<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMail;
use Illuminate\Http\Request;
use App\Models\TdfTransaction;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Mail\TDFTransfer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\support\Facades\Auth;

class TDFTransactionController extends Controller
{
    public function getTDFtransaction()
    {
        $data = TdfTransaction::where([
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }



    public function transferToTDF(Request $request)
    {

        

        if(empty($request->tdfaccount)){
            $success['message'] = 'Account Field Required.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }

        if($request->tdfamount < 18){
            $success['message'] = 'Minimum transfer is £18.00.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }


        if(empty($request->tdfamount)){
            $success['message'] = 'Amount Field Required.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }


        if($request->tdfamount  > (auth()->user()->balance + auth()->user()->overdrawn_amount)){
            $success['message'] = 'You don\'t have enough balance to transfer.';
            return response()->json(['success'=>false,'response'=> $success], 202);
            exit();
        }
        
        $data = new TdfTransaction();
        $data->issue_date = date('Y-m-d');
        $data->user_id = auth()->user()->id;
        $data->tdfaccount = $request->tdfaccount;
        $data->tdf_amount = $request->tdfamount;
        $data->current_dollar_amount = $request->tdfamount;
        if ($data->save()) {

            $user = User::find(auth()->user()->id);
            $user->balance = $user->balance - $request->tdfamount;
            $user->save();

            // card balance update
            if (isset($user->CreditProfileId)) {
                $CreditProfileId = $user->CreditProfileId;
                $CreditProfileName = $user->name;
                $AvailableBalance = 0 - $request->tdfamount;
                $comment = "Transfer to TDF";
                $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                    ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                        'CreditProfileId' => $CreditProfileId,
                        'CreditProfileName' => $CreditProfileName,
                        'AvailableBalance' => $AvailableBalance,
                        'comment' => $comment,
                    ]);
            }
            // card balance update end

            
            $udtransaction = new Usertransaction();
            $udtransaction->t_id = time()."-".auth()->user()->id;
            $udtransaction->user_id = auth()->user()->id;
            $udtransaction->t_type = "Out";
            $udtransaction->amount =  $request->tdfamount;
            $udtransaction->t_unq = time().rand(1,100);
            $udtransaction->title ="Transfer to TDF";
            $udtransaction->status =  1;
            $udtransaction->save();

            $transaction = new Transaction();
            $transaction->t_id = time()."-".auth()->user()->id;
            $transaction->user_id = auth()->user()->id;
            $transaction->t_type = "Out";
            $transaction->amount =  $request->tdfamount;
            $transaction->note ="Transfer to TDF";
            $transaction->status =  1;
            $transaction->save();

            
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $array['name'] = auth()->user()->name;
            $array['subject'] = 'Urgent request';
            $array['from'] = 'info@tevini.co.uk';
            $array['cc'] = $contactmail;
            $email = auth()->user()->email;

            Mail::to($email)
                    ->cc($contactmail)
                    ->send(new TDFTransfer($array));


            $success['message'] = 'TDF transferred successfully.';
            $success['data'] = $data;
            $success['balance'] = Auth::user()->balance;
            return response()->json(['success'=>true,'response'=> $success], 200);
        } else {
            return response()->json(['status'=> 303,'message'=>'Server error!!']);
        }
        
    }
}
