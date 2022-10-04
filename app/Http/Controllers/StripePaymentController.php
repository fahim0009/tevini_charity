<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Models\Donation;
use App\Models\StripeTopup;
use App\Models\Fundraiser;
use Illuminate\Support\Facades\Auth;

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('frontend.user.strip_topup');
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        if($request->typeof=='stripeTopup'){
            $amt = $request->amount;
            // Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            Stripe\Stripe::setApiKey("sk_test_51HI5BcCKybLLVMsSAFPeM6AX1HSb250L8EiFSAFluSOb1dMkWOF4WRnAhweXdayytuigBDLHbUtjNHUMZvWITo8X00mRHKYvxs");
            Stripe\Charge::create ([
                    "amount" => $amt * 100,
                    "currency" => "usd",
                    "source" => $request->stripeToken,
                    "description" => "Tevini donor topup"
            ]);

            $stripetopup = new StripeTopup();
            $stripetopup->donor_id = $request->donor_id;
            $stripetopup->amount = $amt;
            $stripetopup->token = time();
            $stripetopup->description = "Tevini donor topup";
            $stripetopup->notification = "1";
            $stripetopup->status = "0";
            $stripetopup->save();

            Session::flash('success', 'Payment successful!');
            return redirect()->route('stripeDonation');
        }
    }


    public function stripetopup()
    {
        $stripe = StripeTopup::all();
        return view('stripe.stripetopup', compact('stripe'));
    }

    public function stripetopupstatus(Request $request)
    {
        $data = StripeTopup::find($request->id);
        $data->status = 1;
        if($data->save()){
            $message ="<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Active Successfully.</b></div>";
            return response()->json(['status'=> 300,'message'=>$message]);
        }

    }
}
