<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Models\Donation;
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
            // $donation = new Donation();
            // $donation->user_id = $request->userid;
            // $donation->project_id = $request->projectid;
            // if(!empty(Auth::user()->id)){
            //     $donation->donar_id = Auth::user()->id;
            // }else{
            //     $donation->donar_id = 'guest';
            // }
            // $fundamount = $request->tdonation - $request->fundcommission;
            // $donation->email = $request->email;
            // $donation->fname = $request->fname;
            // $donation->lname =  $request->lname;
            // $donation->country = $request->country;
            // $donation->postcode = $request->postcode;

            // $donation->amount = $fundamount;
            // $donation->charge = $request->fundcommission;
            // $donation->total_amount = $request->tdonation;

            // $donation->cname = $request->cname;
            // $donation->cnumber = $request->cnumber;
            // $donation->cvv = $request->cvv;
            // $donation->mm = $request->mm;
            // $donation->yy = $request->yy;
            // $donation->status = 0;
            // if($donation->save()){

            //     $where = [
            //         'id'=> $request->projectid
            //     ];
            //     $user = Fundraiser::where($where)->get()->first();
            //     $user->balance = $user->balance+$fundamount;
            //     $user->save();

            //     Session::flash('success', 'Payment successful!');
            //     return redirect()->route('single-fundraiser', encrypt($request->projectid));

            // }
            // else{
            //     return back();
            // }

            Session::flash('success', 'Payment successful!');
            return redirect()->route('stripeDonation');
        }




    }
}
