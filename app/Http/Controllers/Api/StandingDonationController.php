<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\StandingDonation;
use App\Models\Charity;
use App\Models\User;
use App\Models\ContactMail;
use App\Mail\DonationstandingReport;
use Auth;
use Illuminate\Support\Facades\Http;

class StandingDonationController extends Controller
{
    public function standingDonationStore(Request $request)
    {


        if ((empty($request->charity_id))) {
            $success['message'] = 'Please select beneficiary field..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if ((empty($request->amount))) {
            $success['message'] = 'Please fill amount field..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if(($request->standard == "true") && ($request->payments_type == "1") && (empty($request->number_payments))){
            
            $success['message'] = 'You have to fill number of payment field!.';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }
        

        if ($request->c_donation == "false") {
            $success['message'] = 'Please accept condition..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        $data = new StandingDonation;
        $data->user_id = Auth::user()->id;
        $data->charity_id = $request->charity_id;
        $data->amount = $request->amount;
        $data->currency = "GBP";
        $data->ano_donation = $request->ano_donation;
        $data->standing_order = $request->standard;
        $data->payments = $request->payments_type;
        $data->number_payments = $request->number_payments;
        $data->payment_made = 0;
        $data->starting = $request->starting;
        $data->interval = $request->interval;   
        $data->charitynote = $request->charitynote;
        $data->mynote = $request->mynote;
        $data->notification = 1;
        $data->status = 0;

        if($data->save()){

            $user = User::where('id',Auth::user()->id)->first();
            $contactmail = ContactMail::where('id', 1)->first()->name;
            $charity = Charity::where('id',$request->charity_id)->first();
            $donation = StandingDonation::where('id',$data->id)->first();

            $array['name'] = $user->name;
            $array['donation'] = $donation;
            $array['cc'] = $contactmail;
            $array['client_no'] = $user->accountno;
            $email = $user->email;
            $array['amount'] = $request->amount;
            $array['charity_note'] = $request->charitynote;
            $array['charity_name'] = Charity::where('id',$request->charity_id)->first()->name;

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationstandingReport($array));

            $success['message'] = 'Donation submited Successfully.';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }


    }

}
