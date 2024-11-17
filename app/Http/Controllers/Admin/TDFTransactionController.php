<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TDFCompleteMail;
use App\Models\ContactMail;
use App\Models\TdfTransaction;
use App\Models\User;
use App\Models\Usertransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class TDFTransactionController extends Controller
{
    public function getTDFTransaction(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->where('status', 0)->get();
        return view('admin.tdf.transaction', compact('data'));
    }

    public function getTDFTransactionComplete(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->where('status', 1)->get();
        return view('admin.tdf.completetransaction', compact('data'));
    }

    public function getTDFTransactionCancel(Request $request)
    {
        $data = TdfTransaction::orderby('id','DESC')->where('status', 3)->get();
        return view('admin.tdf.canceltransaction', compact('data'));
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


    public function changeStatus(Request $request)
    {
        $user_id = TdfTransaction::where('id',$request->tdfid)->first()->user_id;
        $tdf_amount = TdfTransaction::where('id',$request->tdfid)->first()->tdf_amount;

        if($request->status == "3"){

        $user = User::find($user_id);
        $user->increment('balance',$tdf_amount);
        $user->save();

        $udtransaction = new Usertransaction();
        $udtransaction->t_id = time()."-".$user_id;
        $udtransaction->user_id = $user_id;
        $udtransaction->t_type = "In";
        $udtransaction->amount =  $tdf_amount;
        $udtransaction->t_unq = time().rand(1,100);
        $udtransaction->title ="Return from TDF";
        $udtransaction->status =  1;
        $udtransaction->save();

        // card balance update
        if (isset($user->CreditProfileId)) {
            $CreditProfileId = $user->CreditProfileId;
            $CreditProfileName = $user->name;
            $AvailableBalance = 0 - $tdf_amount;
            $comment = "TDF Transaction Cancel";
            $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
                ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
                    'CreditProfileId' => $CreditProfileId,
                    'CreditProfileName' => $CreditProfileName,
                    'AvailableBalance' => $AvailableBalance,
                    'comment' => $comment,
                ]);
        }
        // card balance update end

        }

        
        $order = TdfTransaction::find($request->tdfid);
        $order->status = $request->status;
        if($order->save()){

            if ($request->status == 1) {
                // email
                    $donor = User::find($user_id);
                    $contactmail = ContactMail::where('id', 1)->first()->name;
                    $array['subject'] = 'TDF transfer complete';
                    $array['from'] = 'info@tevini.co.uk';
                    $array['cc'] = $contactmail;
                    $array['name'] = $donor->name;
                    $email = $donor->email;

                    Mail::to($email)
                    ->cc($contactmail)
                    ->send(new TDFCompleteMail($array));
                // email
            }
            


            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Status change successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }
    }



}
