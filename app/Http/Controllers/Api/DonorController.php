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
use GuzzleHttp\Client;

class DonorController extends Controller
{
    public function userDonationStore(Request $request)
    {


        if ((empty($request->charity_id))) {
            $success['message'] = 'Please select beneficiary field..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if ((empty($request->amount))) {
            $success['message'] = 'Please fill amount field..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        if ($request->c_donation == "false") {
            $success['message'] = 'Please accept condition..';
            return response()->json(['success'=>false,'response'=> $success], 202);
        }

        

        if(($request->standard == "true") && ($request->payments_type == "1") && (empty($request->number_payments))){
            
            $success['message'] = 'You have to fill number of payment field!.';
            return response()->json(['success'=>false,'response'=> $success], 202);
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

            Mail::to($email)
            ->cc($contactmail)
            ->send(new DonationReport($array));

            $success['message'] = 'Donation create Successfully.';
            $success['data'] = $data;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }
    }


    public function userDonationrecod()
    {
        $data = Donation::where([
            ['standing_order','=', 'false'],
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }

    public function userStandingrecod()
    {
        $data = StandingDonation::where([
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        $success['data'] = $data;
        return response()->json(['success'=>true,'response'=> $success], 200);
    }

    public function usersingleStanding($id)
    {

        $singleStddonation = StandingdonationDetail::where([
            ['id','=', $id],
            ['user_id','=', auth()->user()->id]
        ])->orderBy('id','DESC')->get();

        $success['data'] = $singleStddonation;
        return response()->json(['success'=>true,'response'=> $success], 200);

    }


    public function activeStandinguser(Request $request)
    {

        if($request->status==1){
            $active = StandingDonation::where([
                ['id','=', $request->id],
                ['user_id','=', Auth::user()->id],
            ])->first();
            $active->status = $request->status;
            $active->save();

            $success['message'] = 'Active Successfully.';
            $success['data'] = $active;
            return response()->json(['success'=>true,'response'=> $success], 200);

        }else{
            $deactive = StandingDonation::where([
                ['id','=', $request->id],
                ['user_id','=', Auth::user()->id],
            ])->first();
            $deactive->status = $request->status;
            $deactive->save();
            
            $success['message'] = 'Inactive Successfully.';
            $success['data'] = $deactive;
            return response()->json(['success'=>true,'response'=> $success], 200);
        }

    }

    public function getMakeDonation()
    {

        return view('frontend.user.donationapp');
    }

    public function makeDonationAppMessage()
    {
        return view('frontend.user.donationsuccess');
    }

   
}
