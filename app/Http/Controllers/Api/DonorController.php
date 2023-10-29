<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commission;
use Illuminate\Support\Facades\Mail;
use App\Models\Provoucher;
use App\Models\User;
use App\Models\Donation;
use App\Models\StandingDonation;
use App\Models\StandingdonationDetail;
use App\Models\OverdrawnRecord;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Charity;
use App\Models\ContactMail;
use App\Mail\TopupReport;
use App\Mail\DonationReport;
use App\Mail\DonationstandingReport;
use App\Mail\DonerReport;
use App\Mail\DonationreportCharity;
use PDF;
use Hash;
use Validator;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DonorController extends Controller
{
    public function userDonationStore(Request $request)
    {
        

        request()->validate([
            'charity_id' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string', 'max:255']
        ],
        [
            'charity_id.required' => 'You have to choose charity field!',
            'amount.required' => 'You have to fill amount field!'
        ]);
        

        

        if(($request->standard == "true") && ($request->payments_type == "1") && (empty($request->number_payments))){
            $message ="<div class='alert alert-danger'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Please fill number of payments field.</b></div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }
        if($request->c_donation == "false"){
            $message ="<div class='alert alert-danger'>Please accept condition.</div>";
            return response()->json(['status'=> 303,'message'=>$message]);
            exit();
        }

        $data = new Donation;
        $data->user_id = Auth::user()->id;
        $data->charity_id = $request->charity_id;
        $data->amount = $request->amount;
        $data->currency = "GBP";
        $data->ano_donation = $request->ano_donation;
        $data->standing_order = $request->standard;
        $data->confirm_donation = $request->c_donation;
        $data->charitynote = $request->charitynote;
        $data->mynote = $request->mynote;
        $data->notification = 1;
        $data->status = 0;

        if($data->save()){

            $utransaction = new Usertransaction();
            $utransaction->t_id = time() . "-" . Auth::user()->id;
            $utransaction->user_id = Auth::user()->id;
            $utransaction->charity_id = $request->charity_id;
            $utransaction->donation_id = $data->id;
            $utransaction->t_type = "Out";
            $utransaction->amount =  $request->amount;
            $utransaction->title =  "Online Donation";
            $utransaction->status =  1;
            $utransaction->save();

            $user = User::find(Auth::user()->id);
            $user->decrement('balance',$request->amount);
            $user->save();

            $charity = Charity::find($request->charity_id);
            $charity->increment('balance',$request->amount);
            $charity->save();

            $user = User::where('id',Auth::user()->id)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $charity = Charity::where('id',$request->charity_id)->first();
            $donation = Donation::where('id',$data->id)->first();


            // card balance update
            // if (isset(Auth::user()->CreditProfileId)) {
            //     $CreditProfileId = Auth::user()->CreditProfileId;
            //     $CreditProfileName = Auth::user()->name;
            //     $AvailableBalance = 0 - $request->amount;
            //     $comment = "Make a donation or Standing order";
            //     $response = Http::withBasicAuth('TeviniProductionUser', 'hjhTFYj6t78776dhgyt994645gx6rdRJHsejj')
            //         ->post('https://tevini.api.qcs-uk.com/api/cardService/v1/product/updateCreditProfile/availableBalance', [
            //             'CreditProfileId' => $CreditProfileId,
            //             'CreditProfileName' => $CreditProfileName,
            //             'AvailableBalance' => $AvailableBalance,
            //             'comment' => $comment,
            //         ]);
            // }
            // card balance update end


            $array['name'] = $user->name;
            $array['cc'] = $contactmail;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['amount'] = $request->amount;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

            // Mail::to($email)
            // ->cc($contactmail)
            // ->send(new DonationReport($array));

            $success['message'] = 'Invoice Updated successfully';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }


    }
}
